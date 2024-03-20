<?php
$card_id = 1949900414555;

date_default_timezone_set('Asia/Bangkok');

$time_now = date("H:i");
$date_now = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="ZXing for JS">
    <title>ZXing TypeScript | Decoding from camera stream</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.css">
    <link rel="stylesheet" href="../assets/css/homepage.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
</head>

<body>

    <main class="wrapper" style="padding-top:2em">

        <div class="btn-QR">
            <button type="button" data-bs-toggle="modal" data-bs-target="#check-inout-Modal-qr">
                <img src="../IMG/QR.png" alt="" style="width: 10vw; height: 10vw;">QR
            </button>
            <div class="modal fade" id="check-inout-Modal-qr" tabindex="-1" aria-labelledby="exampleModalLabel">
                <div class="modal-dialog">
                    <div class="containerQR">
                        <div class="modal-content">

                            <div class="btnCloseQR">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-headerQR center">
                                <span class="modal-title">ลงชื่อด้วย QR-Code</span>
                            </div>

                            <div class="modal-body">

                                <div class="center">
                                    <video class="center" id="video" width="300" height="300" style="border: 1px solid red"></video>
                                </div>

                                <div class="displayTime-qr center">
                                    <div>
                                        <span class="Topic" style="text-align:center;">สแกน QR-Code เพื่อลงชื่อ</span>
                                    </div>
                                    <div>
                                        <span class="Time" style="text-align:center;"><?php echo "เวลา " . $time_now . " น."; ?></span>
                                    </div>
                                    <div>
                                        <span style="text-align:center;" id="location"></span><br>
                                    </div>
                                </div>

                                <div class="btnQR-check-in center">
                                    <div>
                                        <a class="button" id="startButton">Start</a>
                                        <a class="button" id="resetButton">Reset</a>
                                    </div>
                                </div>

                                <form hidden id="form-check-qr" method="post" enctype="multipart/form-data">
                                    <input type="text" id="qr-card-id" name="qr-card-id" value="<?php echo $card_id; ?>">
                                    <input type="text" id="qr-date" name="qr-date" value="<?php echo $date_now; ?>">
                                    <input type="text" id="qr-time" name="qr-time" value="<?php echo $time_now; ?>">

                                    <input type="text" id="qr-in-coords" name="qr-in-coords" value="">
                                    <input type="text" id="qr-out-coords" name="qr-out-coords" value="">
                                    <input type="text" id="check_type" name="check_type" value="qr_type">
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script type="text/javascript" src="script/zxing-down.js"></script>

    <script type="text/javascript">
        var rearCameraId;

        function checkRearCamera() {
            return new Promise((resolve, reject) => {
                navigator.mediaDevices.enumerateDevices()
                    .then(devices => {
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');
                        const rearCamera = videoDevices.find(device => device.label.toLowerCase().includes('back'));

                        if (rearCamera) {
                            rearCameraId = rearCamera.deviceId;
                            console.log('Device ID ของกล้องหลังคือ:', rearCameraId);
                            $('#cam').text("มีกล้องหลัง");
                            resolve(rearCameraId);
                        } else {
                            console.log('ไม่พบกล้องหลัง');
                            $('#cam').text("ไม่พบกล้องหลัง");
                            reject(new Error('ไม่พบกล้องหลัง'));
                        }

                    })
                    .catch(error => {
                        console.error('เกิดข้อผิดพลาดในการดึงข้อมูลอุปกรณ์วีดีโอ:', error);
                        reject(error);
                    });
            });
        }

        $(document).ready(function() {

            checkRearCamera().then(rearCameraId => {
                let selectedDeviceId;
                const codeReader = new ZXing.BrowserQRCodeReader();
                console.log('ZXing code reader initialized');

                codeReader.getVideoInputDevices()
                    .then((videoInputDevices) => {
                        selectedDeviceId = rearCameraId;
                    })
                    .catch((err) => {
                        console.error(err);
                    });

                $('#startButton').on('click', function() {
                    // decodeContinuously(codeReader, selectedDeviceId);
                    decodeOnce(codeReader, selectedDeviceId);
                    console.log(`Started decode from camera with id ${selectedDeviceId}`);
                });

                $('#resetButton').on('click', function() {
                    codeReader.reset();
                    $('#result').text('');
                    console.log('Reset.');
                });

            }).catch(error => {
                console.error(error);
            });

        });

        function decodeContinuously(codeReader, selectedDeviceId) {
            codeReader.decodeFromInputVideoDeviceContinuously(selectedDeviceId, 'video', function(result, err) {
                if (result) {
                    console.log('Found QR code!', result);
                    handleResult(result);
                }

                if (err) {
                    if (err instanceof ZXing.NotFoundException) {
                        console.log('No QR code found.');
                    }

                    if (err instanceof ZXing.ChecksumException) {
                        console.log('A code was found, but its read value was not valid.');
                    }

                    if (err instanceof ZXing.FormatException) {
                        console.log('A code was found, but it was in an invalid format.');
                    }
                }
            });
        }

        function decodeOnce(codeReader, selectedDeviceId) {
            codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video').then((result) => {
                console.log(result);
                handleResult(result);
            }).catch((err) => {
                console.error(err);
                $('#result').text(err);
            });
        }

        function handleResult(result) {
            if (result) {
                console.log('Found QR code!', result);
                const resultText = result.text;

                // Replace with the actual card_id value from your PHP code
                const card_id = <?php echo $card_id; ?>;

                if (resultText) {
                    console.log('QR Code matched!');

                    // Show SweetAlert2 confirmation popup
                    Swal.fire({
                        title: 'Confirmation',
                        text: 'Do you want to proceed?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User clicked 'Yes', you can perform further actions here
                            Swal.fire('Proceeding!', 'You can add your action here.', 'success');
                        } else {
                            // User clicked 'No' or closed the popup
                            Swal.fire('Cancelled', 'You can handle cancellation here.', 'info');
                        }
                    });

                } else {
                    console.log('QR Code not matched.');
                    $('#result').text("False");
                }
            }
        }
    </script>
</body>

</html>