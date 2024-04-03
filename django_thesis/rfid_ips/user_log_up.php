<?php  
session_start();
?>
<div class="table-responsive" style="max-height: 500px;"> 
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>No.</th>
        <th>Name</th>
        <th>ID</th>
        <th>UHF RFID</th>
        <th>SSID</th>
        <th>Dep</th>
        <th>Date</th>
        <th>Time In</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
      <?php

date_default_timezone_set('Asia/Manila');
$time_sel_start = date('Y-m-d H:i:s');

// Connect to database
require 'connectDB.php';
$searchQuery = "";
$Start_date = "";
$End_date = "";
$End_time = "";
$Card_sel = "";

// Retrieve the time with milliseconds from the GET request
$time_with_milliseconds = isset($_GET['time_with_milliseconds']) ? $_GET['time_with_milliseconds'] : null;



if (isset($_POST['log_date'])) {
    // Start date filter
    if ($_POST['date_sel_start'] != 0) {
        $Start_date = $_POST['date_sel_start'];
        $_SESSION['searchQuery'] = "checkindate='".$Start_date."'";
    } else {
        $Start_date = date("Y-m-d");
        $_SESSION['searchQuery'] = "checkindate='".date("Y-m-d")."'";
    }

    // Time-In filter
    if ($_POST['time_sel'] == "Time_in") {
        // Start time filter
        if ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] == 0) {
            $Start_time = $time_sel_start;
            $_SESSION['searchQuery'] .= " AND timein='".$Start_time."'";
        } elseif ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] != 0) {
            $Start_time = $time_sel_start;
        }
        // End time filter
        if ($_POST['time_sel_end'] != 0) {
            $End_time = $time_sel_start;
            $_SESSION['searchQuery'] .= " AND timein BETWEEN '".$Start_time."' AND '".$End_time."'";
        }
    }

    // Card filter
    if ($_POST['card_sel'] != 0) {
        $Card_sel = $_POST['card_sel'];
        $_SESSION['searchQuery'] .= " AND card_uid='".$Card_sel."'";
    }
    // Department filter
    if ($_POST['dev_uid'] != 0) {
        $dev_uid = $_POST['dev_uid'];
        $_SESSION['searchQuery'] .= " AND device_uid='".$dev_uid."'";
    }
}

if ($_POST['select_date'] == 1) {
    $Start_date = date("Y-m-d");
    $_SESSION['searchQuery'] = "checkindate='".$Start_date."'";
}

// Convert milliseconds to seconds
$time_seconds = round($time_with_milliseconds / 1000); // Assuming milliseconds are received as milliseconds since the epoch

// Convert time to MySQL format (YYYY-MM-DD HH:MM:SS)
$time_mysql = date('Y-m-d H:i:s', $time_seconds);

$sql = "SELECT * FROM users_logs WHERE ".$_SESSION['searchQuery']."  ORDER BY id DESC";
$result = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($result, $sql)) {
    echo '<p class="error">SQL Error</p>';
} else {
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    if (mysqli_num_rows($resultl) > 0){
        while ($row = mysqli_fetch_assoc($resultl)){
            ?>
            <TR>
            <TD><?php echo $row['id'];?></TD>
            <TD><?php echo $row['username'];?></TD>
            <TD><?php echo $row['serialnumber'];?></TD>
            <TD><?php echo $row['card_uid'];?></TD>
            <TD><?php echo $row['ssid'];?></TD>
            <TD><?php echo $row['device_dep'];?></TD>
            <TD><?php echo $row['checkindate'];?></TD>
            <TD><?php echo $row['timein'];?></TD>
            </TR>
            <?php

                }
            }
        }
      ?>
    </tbody>
  </table>
</div>
