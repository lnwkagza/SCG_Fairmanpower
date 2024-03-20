import sched
import time

event_schedule = sched.scheduler(time.time, time.sleep)

def do_something(sc):
    print("Hello, World!")
    event_schedule.enter(30, 1, do_something, (sc,))

event_schedule.enter(30, 1, do_something, (event_schedule,))

if __name__ == "__main__":
    try:
        while True:
            event_schedule.run()
            time.sleep(1)
    except KeyboardInterrupt:
        print("Script terminated by user.")
