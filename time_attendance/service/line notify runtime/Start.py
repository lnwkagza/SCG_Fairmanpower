import win32serviceutil
import win32service
import win32event
import servicemanager
import socket
import os
import sys
import logging
import pyodbc
import requests
import datetime
import time
import json


log_folder = 'logs'
os.makedirs(log_folder, exist_ok=True)

# Configure logging
log_file_path = os.path.join(log_folder, 'logfile.log')
logging.basicConfig(filename=log_file_path, level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

try:
    with open('config/connect.config', 'r') as config_file:
        config_data = json.load(config_file)

    connection = pyodbc.connect(
        'DRIVER={SQL Server};'
        f'SERVER={config_data["server"]};'
        f'DATABASE={config_data["database"]};'
        f'UID={config_data["uid"]};'
        f'PWD={config_data["pwd"]};'
    )

    cursor = connection.cursor()
    print("Database connection successful")
    logging.info("Database connection successful")
except Exception as e:
    print(f"Error connecting to the database: {e}")
    logging.info(F"Error connecting to the database: {e} ")
    exit()

today = datetime.datetime.today()
days_of_week = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
print(f"Today is: {days_of_week[today.weekday()]} {today}")

class MyService(win32serviceutil.ServiceFramework):
    _svc_name_ = "LineNotifyRunning"
    _svc_display_name_ = "LineNotifyRunning"

    def __init__(self, args):
        win32serviceutil.ServiceFramework.__init__(self, args)
        self.hWaitStop = win32event.CreateEvent(None, 0, 0, None)
        socket.setdefaulttimeout(60)
        self.is_alive = True

    def SvcStop(self):
        self.ReportServiceStatus(win32service.SERVICE_STOP_PENDING)
        win32event.SetEvent(self.hWaitStop)
        self.is_alive = False

    def SvcDoRun(self):
        servicemanager.LogMsg(servicemanager.EVENTLOG_INFORMATION_TYPE, servicemanager.PYS_SERVICE_STARTED, (self._svc_name_, ''))
        self.main()

    def main(self):
        while self.is_alive:
            try:
                self.run_line_notify()
                time.sleep(60 * 1)  # Delay for 1 minute (adjust as needed)
            except Exception as e:
                logging.exception("An error occurred: %s", str(e))

    def send_line_notify(self, msg, token, employee_id):
        url = 'https://notify-api.line.me/api/notify'
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Authorization': f'Bearer {token}'
        }
        message = {'message': f'{msg}'}

        try:
            response = requests.post(url, data=message, headers=headers)
            response.raise_for_status()
            print(f"Line notification sent to {employee_id} = {today} ok")
            logging.info(f"Line notification sent to {employee_id} = {today} ok")
        except requests.exceptions.RequestException as e:
            print(f"Error sending Line Notify: {e}")
            logging.info(f"Error sending Line Notify: {e} {today}")

    delay_minutes = 1  # Adjust as needed

    print("------------------------------")
    print("Line Notify message Running...")
    print("------------------------------")
    while True:

        start_time = time.time()
        now = datetime.datetime.now()

        with open('config/timeSet.config', 'r') as config_file:
            config_data = json.load(config_file)
            
        time_day_start = datetime.time(*config_data["time_day_start"])
        time_day_end = datetime.time(*config_data["time_day_end"])
        time_week = datetime.time(*config_data["time_week"])
        day_week = config_data["day_week"]

        # Check for daily notification
        if time_day_start <= now.time() <= time_day_end:
            print(f"Day notification triggered in {days_of_week[today.weekday()]} {now}")
            logging.info(f"Day notification triggered in {days_of_week[today.weekday()]} {now}")
            print("------------------------------")
            logging.info(F"------------------------------")
            
            sql = """
                    SELECT
                        login.line_token,
                        login.card_id,
                        employee.firstname_thai,
                        employee.lastname_thai,
                        COALESCE(absence_record.absence_record_waiting_status, 0) AS absence_record_waiting_status,
                        COALESCE(check_inout.check_inout_waiting_status, 0) AS check_inout_waiting_status
                    FROM
                        login
                    INNER JOIN
                        employee ON login.card_id = employee.card_id
                    LEFT JOIN (
                        SELECT
                            approver,
                            COUNT(approve_status) AS absence_record_waiting_status
                        FROM
                            absence_record
                        WHERE
                            approve_status IS NOT NULL
                        GROUP BY
                            approver
                    ) AS absence_record ON employee.card_id = absence_record.approver
                    LEFT JOIN (
                        SELECT
                            approver,
                            COUNT(approve_status) AS check_inout_waiting_status
                        FROM
                            check_inout
                        WHERE
                            approve_status IS NOT NULL
                        GROUP BY
                            approver
                    ) AS check_inout ON employee.card_id = check_inout.approver
                    WHERE
                        absence_record.absence_record_waiting_status > 0 OR check_inout.check_inout_waiting_status > 0
            """
            cursor.execute(sql)
            results = cursor.fetchall()
            for row in results:
                line_token = row[0]
                id = row[1]
                firstname_thai = row[2]
                lastname_thai = row[3]
                absence_record_waiting_status = row[4]
                check_inout_waiting_status = row[5]

                # You can use the retrieved data to construct your Line Notify message
                if absence_record_waiting_status > 0 and check_inout_waiting_status == 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} มีคำขอลาของพนักงานที่คุณยังไม่อนุมัติจำนวน {absence_record_waiting_status} รายการ"
                elif absence_record_waiting_status > 0 and check_inout_waiting_status > 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} มีคำขอลาและคำแก้เช็คอินของพนักงานที่คุณยังไม่อนุมัติ ลา {absence_record_waiting_status} รายการ เช็คอิน {check_inout_waiting_status} รายการ"
                elif absence_record_waiting_status == 0 and check_inout_waiting_status > 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} มีคำแก้เช็คอินของพนักงานที่คุณยังไม่อนุมัติจำนวน {check_inout_waiting_status} รายการ"
                # Send Line Notify message
                self.send_line_notify(message, line_token, id)

        # Check for weekly notification every Monday
        if today.weekday() == day_week and now.time().hour == time_week.hour and now.time().minute == time_week.minute:
            print(f"Weekly notification triggered in {days_of_week[today.weekday()]} {now}")
            logging.info(F"Weekly notification triggered in {days_of_week[today.weekday()]} {now}")
            print("------------------------------")
            logging.info(F"------------------------------")

            sql = """
                    SELECT
                        login.line_token,
                        employee.card_id,
                        employee.firstname_thai,
                        employee.lastname_thai,
                        COUNT(CASE WHEN check_inout.check_status = 'ขาดงาน' THEN 1 END) AS COUNT_status_misswork,
                        COUNT(CASE WHEN check_inout.check_status = 'มาสาย' THEN 1 END) AS COUNT_status_late,
                        COUNT(CASE WHEN check_inout.check_status = 'กลับก่อน' THEN 1 END) AS COUNT_status_early_leave
                    FROM
                        login
                    INNER JOIN
                        employee ON login.card_id = employee.card_id
                    INNER JOIN
                        check_inout ON employee.card_id = check_inout.card_id -- Assuming there is a card_id in both employee and check_inout tables
                    GROUP BY
                        login.line_token,
                        employee.card_id,
                        employee.firstname_thai,
                        employee.lastname_thai
                    HAVING
                        COUNT(CASE WHEN check_inout.check_status = 'ขาดงาน' THEN 1 END) > 0 OR
                        COUNT(CASE WHEN check_inout.check_status = 'มาสาย' THEN 1 END) > 0 OR
                        COUNT(CASE WHEN check_inout.check_status = 'กลับก่อน' THEN 1 END) > 0;
            """
            cursor.execute(sql)
            results = cursor.fetchall()
            for row in results:
                line_token = row[0]
                id = row[1]
                firstname_thai = row[2]
                lastname_thai = row[3]
                COUNT_status_misswork = row[4]
                COUNT_status_late = row[5]
                COUNT_status_early_leave = row[6]

                if COUNT_status_misswork > 0 and COUNT_status_late == 0 and COUNT_status_early_leave == 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} ในสัปดาห์นี้คุณมี {COUNT_status_misswork} รายการขาดงาน"
                elif COUNT_status_misswork > 0 and COUNT_status_late > 0 and COUNT_status_early_leave == 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} ในสัปดาห์นี้คุณมี {COUNT_status_misswork} รายการขาดงาน และ {COUNT_status_late} รายการ มาสาย"
                elif COUNT_status_misswork == 0 and COUNT_status_late > 0 and COUNT_status_early_leave == 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} ในสัปดาห์นี้คุณมี {COUNT_status_late} รายการมาสาย"
                elif COUNT_status_misswork > 0 and COUNT_status_late == 0 and COUNT_status_early_leave > 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai} ในสัปดาห์นี้คุณมี {COUNT_status_misswork} รายการ ขาดงาน และ {COUNT_status_early_leave} รายการ กลับก่อน"
                elif COUNT_status_misswork == 0 and COUNT_status_late == 0 and COUNT_status_early_leave > 0:
                    message = f"เรียนคุณ {firstname_thai} {lastname_thai}ในสัปดาห์นี้คุณมี {COUNT_status_early_leave} รายการกลับก่อน"
                
                # Send Line Notify message
                send_line_notify(message, line_token, id)

        time.sleep(delay_minutes * 60)
        
        if _svc_name_ == '__main__':
            if len(sys.argv) == 1:
                servicemanager.Initialize()
                servicemanager.PrepareToHostSingle(MyService)
                servicemanager.StartServiceCtrlDispatcher()
            else:
                win32serviceutil.HandleCommandLine(MyService)