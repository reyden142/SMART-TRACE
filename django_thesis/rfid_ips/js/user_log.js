$(document).ready(function(){
  // Get Report passenger
  $(document).on('click', '#user_log', function(){
	  
	// CHATGPT edited   
	// Get the current date and time in the local time zone
	/*
	var localDateTime = new Date();
	
	// Set the time zone options for Manila (UTC+8)
	var timeZone = 'Asia/Manila';
	var timeZoneOptions = { timeZone: timeZone };

	// Format the local date and time as a string in the Manila time zone
	var localDateTimeString = localDateTime.toLocaleString('en-PH', timeZoneOptions);

	// Set the time_sel_start variable to the local date and time string
	var time_sel_start = localDateTimeString;
	var time_sel_end = localDateTimeString;
	*/
	
	// Get the current time in Manila
	var timeZone = 'Asia/Manila';
	var localDateTime = new Date();
	var options = { timeZone: timeZone };
	var time_sel_start = localDateTime.toLocaleString('en-PH', options);


    
    var date_sel_start = $('#date_sel_start').val();
    var date_sel_end = $('#date_sel_end').val();
    var time_sel = $(".time_sel:checked").val();
    var time_sel_start = $('#time_sel_start').val();
    var time_with_milliseconds = $('#time_with_milliseconds').val();
    var time_sel_end = $('#time_sel_end').val();
    var card_sel = $('#card_sel option:selected').val();
    var dev_uid = $('#dev_sel option:selected').val();
    var ssid = $('#ssid').val();
    var Birthdate = $('#Birthdate').val();
    var Contact = $('#Contact').val();
    var EmergencyContact = $('#EmergencyContact').val();
    var ValidationPeriod = $('#ValidationPeriod').val();
    var MedicalHistory = $('#MedicalHistory').val();
    
    $.ajax({
      url: 'user_log_up.php',
      type: 'POST',
      data: {
        'log_date': 1,
        'date_sel_start': date_sel_start,
        'date_sel_end': date_sel_end,
        'time_sel': time_sel,
        'time_sel_start': time_sel_start,
       'time_with_milliseconds':time_with_milliseconds,
        'time_sel_end': time_sel_end,
        'card_sel': card_sel,
        'dev_uid': dev_uid,
        'ssid': ssid,
        'Birthdate': Birthdate,
        'Contact': Contact,
        'EmergencyContact': EmergencyContact,
        'EmergencyContact': EmergencyContact,
        'ValidationPeriod': ValidationPeriod,
        'MedicalHistory': MedicalHistory,
      },
      success: function(response){

        $('.up_info2').fadeIn(500);
        $('.up_info2').text("The Filter has been selected!");

        $('#Filter-export').modal('hide');
        setTimeout(function () {
            $('.up_info2').fadeOut(500);
        }, 5000);

        $.ajax({
          url: "user_log_up.php",
          type: 'POST',
          data: {
            'log_date': 1,
            'date_sel_start': date_sel_start,
            'date_sel_end': date_sel_end,
            'time_sel': time_sel,
            'time_sel_start': time_sel_start,
            'time_with_milliseconds':time_with_milliseconds,
            'time_sel_end': time_sel_end,
            'dev_uid': dev_uid,
            'card_sel': card_sel,
            'ssid': ssid,
            'Birthdate': Birthdate,
            'Contact': Contact,
            'EmergencyContact': EmergencyContact,
            'ValidationPeriod': ValidationPeriod,
            'MedicalHistory': MedicalHistory,
            'select_date': 0,
          }
          }).done(function(data) {
          $('#userslog').html(data);
        });
      }
    });
  });
});