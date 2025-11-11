<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>

    <body>
        <link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad&skip_first_step=<?php echo @$_GET['skip_first_step']; ?>&search_location_id=<?php echo @$_GET['search_location_id']; ?>&search_dropoff_id=<?php echo @$_GET['search_dropoff_id']; ?>&search_passengers_from_to=<?php echo @$_GET['search_passengers_from_to']; ?>&search_date=<?php echo @$_GET['search_date']; ?><?php echo isset($_GET['loadSummary']) ? ('&loadSummary='.$_GET['loadSummary']) : '';?><?php echo isset($_GET['booking_id']) ? ('&booking_id='.$_GET['booking_id']) : '';?><?php echo isset($_GET['loadPayment']) ? ('&loadPayment='.$_GET['loadPayment']) : '';?><?php echo isset($_GET['booking_uuid']) ? ('&booking_uuid='.$_GET['booking_uuid']) : '';?>"></script>
    </body>
</html>