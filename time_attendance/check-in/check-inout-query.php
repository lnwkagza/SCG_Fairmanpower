<?php
// include("../database/connectdb.php");

$card_id = $_SESSION["card_id"];


$CoordsQuery = "SELECT * FROM location_coords_default WHERE coords_id = ?";
$CoordsQueryParams = array(1);
$coords_default_query = sqlsrv_query($conn, $CoordsQuery, $CoordsQueryParams);

if ($coords_default_query === false) {
    die(print_r(sqlsrv_errors(), true));
}

$result_coords = sqlsrv_fetch_array($coords_default_query, SQLSRV_FETCH_ASSOC);

//แยก string ด้วยเงื่อนไข (,)
$coords_str = explode(',', $result_coords['coords_in_lat_lng']);
$coords_range = $result_coords['coords_range'];

$lat = $coords_str[0];
$lng = $coords_str[1];

//1949999999904

$checkINOUT_tempo_Query = "SELECT * FROM shift_work_location_temporary_request
                        LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                        WHERE card_id = ?
                        AND shift_start_date IS NOT NULL
                        AND shift_end_date IS NOT NULL
                        AND approval_id = 3
                        AND (inspector_id = 6 OR inspector_id IS NULL)
";

$checkINOUT_permit_Query = "SELECT * FROM shift_work_location_temporary_request
                        LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                        WHERE card_id = ?
                        AND shift_start_date IS NOT NULL
                        AND shift_end_date IS NULL
                        AND approval_id = 3
                        AND (inspector_id = 6 OR inspector_id IS NULL)
";

$checkINOUT_tempo_Params = array($card_id);
$checkINOUT_tempo_stmt = sqlsrv_query($conn, $checkINOUT_tempo_Query, $checkINOUT_tempo_Params);

$checkINOUT_permit_Params = array($card_id);
$checkINOUT_permit_stmt = sqlsrv_query($conn, $checkINOUT_permit_Query, $checkINOUT_permit_Params);

if ($checkINOUT_tempo_stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($checkINOUT_permit_stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>

<script>
    const default_coords = {
        lat: <?php echo $lat; ?>,
        lng: <?php echo $lng; ?>
    };

    const gps_range = <?php echo $coords_range ?>;
    // const gps_range = 2000;

    const secretKey = "SCG_Thungsong_thai";
</script>