<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/RFID_STICKER_LOGO.jpg" href="">



    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>
<body>

<?php include'header.php'; ?>

<main>
<section>
  <h1 class="slideInDown animated">Here are all the Users</h1>
  <!--User table-->
  <div class="table-responsive slideInRight animated" style="max-height: 400px;">
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th>Name</th>
          <th>ID</th>
          <th>sex</th>
          <th>UHF RFID</th>
          <th>Date</th>
          <th>Deparment</th>
          <th>SSID</th>
          <th>Birthdate</th>
          <th>Contact</th>
          <th>Emergency Contact</th>
          <th>Validation Period</th>
          <th>Medical History</th>

        </tr>
      </thead>
      <tbody class="table-secondary">
        <?php
          //Connect to database
          require'connectDB.php';

            $sql = "SELECT * FROM users WHERE add_card=1 ORDER BY id DESC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<p class="error">SQL Error</p>';
            }
            else{
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
              if (mysqli_num_rows($resultl) > 0){
                  while ($row = mysqli_fetch_assoc($resultl)){
          ?>
                      <TR>
                      <TD><?php echo $row['id']; echo" | "; echo $row['username'];?></TD>
                      <TD><?php echo $row['serialnumber'];?></TD>
                      <TD><?php echo $row['sex'];?></TD>
                      <TD><?php echo $row['card_uid'];?></TD>
                      <TD><?php echo $row['user_date'];?></TD>
                      <TD><?php echo $row['device_dep'];?></TD>
                      <TD><?php echo $row['ssid'];?></TD>
                      <TD><?php echo $row['Birthdate'];?></TD>
                      <TD><?php echo $row['Contact'];?></TD>
                      <TD><?php echo $row['EmergencyContact'];?></TD>
                       <TD><?php echo $row['ValidationPeriod'];?></TD>
                       <TD><?php echo $row['MedicalHistory'];?></TD>

                      </TR>
        <?php
                }   
            }
          }
        ?>
      </tbody>
    </table>
  </div>
</section>
</main>
</body>
</html>