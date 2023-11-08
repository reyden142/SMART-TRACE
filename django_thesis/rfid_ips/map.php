<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: map.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/RFID_STICKER_LOGO.jpg" href="">

    <link rel="stylesheet" type="text/css" href="css/map.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/manage_users.js"></script>

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

<?php include'header.php';?>

<main>
<h1 class="slideInDown animated">Register Users</h1>
	<div class="form-style-5 slideInDown animated">
		<form enctype="multipart/form-data">
			<div class="alert_user"></div>
			<fieldset>
				<legend><span class="number">1</span> User Info</legend>
				<input type="hidden" name="user_id" id="user_id">
				<input type="text" name="name" id="name" placeholder="Name">
				<input type="text" name="number" id="number" placeholder="ID Number">
				<input type="email" name="email" id="email" placeholder="Email">
			    <label for="sex"><b> Sex:</b></label>
			    <input type="radio" name="sex" class="sex" value="Female"> Female

	          	<input type="radio" name="sex" class="sex" value="Male" checked="checked"> Male
			</fieldset>
			<fieldset>
			<legend><span class="number">2</span> Additional Info</legend>
			<label>
				<label for="Device"><b>User Department:</b></label>
                    <select class="dev_sel" name="dev_sel" id="dev_sel" style="color: #001;">
                      <option value="0">All Departments</option>
                      <?php
                        require'connectDB.php';
                        $sql = "SELECT * FROM devices ORDER BY device_name ASC";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo '<p class="error">SQL Error</p>';
                        }
                        else{
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            while ($row = mysqli_fetch_assoc($resultl)){
                      ?>
                              <option value="<?php echo $row['device_uid'];?>"><?php echo $row['device_dep']; ?></option>
                      <?php
                            }
                        }
                      ?>
                    </select>
                    <input type="text" name="Macaddress" id="Macaddress" placeholder="Macaddress">
                    <input type="text" name="Birthdate" id="Birthdate" placeholder="Birthdate">
                    <input type="text" name="Contact" id="Contact" placeholder="Contact number">
                    <input type="text" name="EmergencyContact" id="EmergencyContact" placeholder="Emergency number">
                    <input type="text" name="ValidationPeriod" id="ValidationPeriod" placeholder="Validation Period">
                    <input type="text" name="MedicalHistory" id="MedicalHistory" placeholder="Medical History">

			</fieldset>
			<button type="button" name="user_add" class="user_add">Add User</button>
			<button type="button" name="user_upd" class="user_upd">Update User</button>
			<button type="button" name="user_rmo" class="user_rmo">Remove User</button>


<section class="mapboxgl-canvas" tabindex="0" aria-label="Map" role="region" width="1336" height="500" style="width: 1200px; height: 1200px;">
  <head>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Display a map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
      #map {position: absolute; top: 0; right: 0; bottom: 0; left: 0;}
    </style>
  </head>
  <body>
    <div id="map">
      <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
    </div>
    <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>
    <script>
      const key = 'YOUR_MAPTILER_API_KEY_HERE';
      const map = L.map('map').setView([7.06569722, 125.59678861], 14); //starting position
      L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=aF7HhncV5bhT2pqqWdRV`,{ //style URL
        tileSize: 512,
        zoomOffset: -1,
        minZoom: 1,
        attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
        crossOrigin: true
      }).addTo(map);

      var myGeoJSON = {"type": "FeatureCollection", "features": [{"type": "Feature", "geometry": {"type": "Polygon", "coordinates": [[[125.59640375, 7.06583173], [125.59632507, 7.06562943], [125.59634742, 7.06556644], [125.59637603, 7.06556378], [125.59639928, 7.06551586], [125.59637156, 7.06544222], [125.59684363, 7.06527896], [125.59699652, 7.06568533], [125.59704569, 7.0657084], [125.59718248, 7.0656596], [125.59725222, 7.06585037], [125.59698311, 7.06594619], [125.59699115, 7.06598168], [125.59662011, 7.06611566], [125.59658167, 7.06602072], [125.5966228, 7.06600653], [125.59664604, 7.06593288], [125.59640375, 7.06583173]]]}, "id": "434c991f-7512-42d6-a587-deac996c0250", "properties": {"name": "BE_Building", "BE_Building": ""}}]}
      L.geoJSON(myGeoJSON).addTo(map)

    </script>
  </body>

</section>


</main>
</body>
</html>


