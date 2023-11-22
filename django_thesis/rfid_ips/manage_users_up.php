
<div class="table-responsive-sm" style="max-height: 870px;">
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>UHF RFID</th>
        <th>FullName</th>
        <th>sex</th>
        <th>ID</th>
        <th>Date</th>
        <th>SSID</th>
        <th>Birthdate</th>
        <th>Contact</th>
        <th>EmergencyContact</th>
        <th>ValidationPeriod</th>
        <th>MedicalHistory</th>



      </tr>
    </thead>
    <tbody class="table-secondary">
    <?php
      //Connect to database
      require'connectDB.php';

        $sql = "SELECT * FROM users ORDER BY id DESC";
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
                  	<TD><?php  
                    		if ($row['card_select'] == 1) {
                    			echo "<span><i class='glyphicon glyphicon-ok' title='The selected UID'></i></span>";
                    		}
                        $card_uid = $row['card_uid'];
                    	?>
                    	<form>
                    		<button type="button" class="select_btn" id="<?php echo $card_uid;?>" title="select this UID"><?php echo $card_uid;?></button>
                    	</form>
                    </TD>
                  <TD><?php echo $row['username'];?></TD>
                  <TD><?php echo $row['sex'];?></TD>
                  <TD><?php echo $row['serialnumber'];?></TD>
                  <TD><?php echo $row['user_date'];?></TD>
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