<?php
session_start(); 
$id = $_SESSION['card_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Information</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <label for="shiftDate">Select Date:</label>
    <input type="date" id="shiftDate" name="date">
    <p id="shiftid"></p>
    <p id="shiftname"></p>

    <script>
    $(document).ready(function() {
        $('#shiftDate').on('change', function() {
            var dateValue = $(this).val();

            if (dateValue.trim() === '') {
                console.error('Please select a valid date.');
                // You can provide user feedback here, such as displaying a message on the page.
                return;
            }

            $.ajax({
                type: 'GET',
                url: '../processing/process_shift_date.php',
                data: {
                    'id': '<?php echo $id; ?>',
                    'date': dateValue,
                },
                success: handleSuccess,
                error: handleAjaxError
            });
        });

        function handleSuccess(response) {
            try {
                console.log('Full response:', response);

                if (typeof response === 'object') {
                    // If the response is already an object, use it directly
                    handleResponse(response);
                } else {
                    var result = JSON.parse(response);
                    handleResponse(result);
                }
            } catch (error) {
                console.log('Error parsing JSON:', error);
                // Provide user feedback for the parsing error, if needed.
            }
        }

        function handleResponse(result) {
            if (result.result === 'success') {
                console.log('Message:', result.message);

                var label;

                switch (result.message) {
                    case 'DD01':
                        label = 'ปกติ 1';
                        break;
                    case 'DD02':
                        label = 'ปกติ 1';
                        break;
                    case 'HOLIDAY':
                        label = 'นักขัต';
                        break;
                    case 'LEAVE':
                        label = 'ลา';
                        break;
                    case 'OFF':
                        label = 'หยุด';
                        break;
                    case 'SA01':
                        label = 'กะ 1';
                        break;
                    case 'SB01':
                        label = 'กะ 2';
                        break;
                    case 'SC01':
                        label = 'กะ 3';
                        break;
                    case 'TRAIN':
                        label = 'อบรม';
                        break;
                    default:
                        label = 'Unknown';
                        break;
                }

                $('#shiftid').text(result.message);
                $('#shiftname').text(label);
            } else {
                console.error('Error during date processing:', result.message);
                // Provide user feedback for the error, if needed.
            }
        }

        function handleAjaxError(xhr, status, error) {
            console.error('Error during AJAX request:', status, error);
            // Provide user feedback for the AJAX error, if needed.
        }
    });
    </script>

</body>

</html>