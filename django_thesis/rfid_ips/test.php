<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';
?>

<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="UTF-8">


    <link rel="apple-touch-icon" type="image/png" href="https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png">

    <meta name="apple-mobile-web-app-title" content="CodePen">

    <link rel="shortcut icon" type="image/x-icon" href="https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico">

    <link rel="mask-icon" type="image/x-icon" href="https://cpwebassets.codepen.io/assets/favicon/logo-pin-b4b4269c16397ad2f0f7a01bcdf513a1994f4c94b8af2f191c09eb0d601762b1.svg" color="#111">




    <script src="https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-2c7831bb44f98c1391d6a4ffda0e1fd302503391ca806e7fcc7b9b87197aec26.js"></script>


  <title>CodePen - Animated CSS Circle Stats with Number Counter</title>

    <link rel="canonical" href="https://codepen.io/epogeedesign/pen/NWqoXRo">


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

<style>
html,
body {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  position: relative;
  background: #002134;
}
.charts_orb {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  flex-wrap: wrap;
  font-family: arial;
  color: white;
}
.charts_orb .orb {
  padding: 20px;
}
.charts_orb .orb .orb_graphic {
  position: relative;
}
.charts_orb .orb .orb_graphic .orb_value {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5em;
  font-weight: bold;
}
.charts_orb .orb .orb_label {
  text-transform: uppercase;
  text-align: center;
  margin-top: 1em;
}
.charts_orb svg {
  width: 110px;
  height: 110px;
}
.charts_orb svg circle {
  transform: rotate(-90deg);
  transform-origin: 50% 50%;
  stroke-dasharray: 314.16, 314.16;
  stroke-width: 2;
  fill: transparent;
  r: 50;
  cx: 55;
  cy: 55;
}
.charts_orb svg circle.fill {
  stroke: #D3D3D3;
}
.charts_orb svg circle.progress {
  stroke: #FF6B00;
  transition: stroke-dashoffset 0.35s;
  stroke-dashoffset: 214.16;
  -webkit-animation: NAME-YOUR-ANIMATION 1.5s forwards;
  -webkit-animation-timing-function: linear;
}
@-webkit-keyframes NAME-YOUR-ANIMATION {
  0% {
    stroke-dashoffset: 314.16;
  }
  100% {
    stroke-dashoffset: 0;
  }
}
</style>

  <script>
  window.console = window.console || function(t) {};
</script>



</head>

<body translate="no">
  <section class="charts_orb">
	<article class="orb">
		<div class="orb_graphic">
			<svg>
				<circle class="fill"></circle>
				<circle class="progress"></circle>
			</svg>
			<div class="orb_value count">306</div>
		</div>
		<div class="orb_label">
			Lorem Ipsum
		</div>
	</article>

	<article class="orb">
		<div class="orb_graphic">
			<svg>
				<circle class="fill"></circle>
				<circle class="progress"></circle>
			</svg>
			<div class="orb_value count">136</div>
		</div>
		<div class="orb_label">
			Lorem Ipsum
		</div>
	</article>

	<article class="orb">
		<div class="orb_graphic">
			<svg>
				<circle class="fill"></circle>
				<circle class="progress"></circle>
			</svg>
			<div class="orb_value count">41</div>
		</div>
		<div class="orb_label">
			Lorem Ipsum
		</div>
	</article>

	<article class="orb">
		<div class="orb_graphic">
			<svg>
				<circle class="fill"></circle>
				<circle class="progress"></circle>
			</svg>
			<div class="orb_value count">52</div>
		</div>
		<div class="orb_label">
			Lorem Ipsum
		</div>
	</article>

	<article class="orb">
		<div class="orb_graphic">
			<svg>
				<circle class="fill"></circle>
				<circle class="progress"></circle>
			</svg>
			<div class="orb_value count">72</div>
		</div>
		<div class="orb_label">
			Lorem Ipsum
		</div>
	</article>
</section>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script id="rendered-js">
$('.count').each(function () {
  $(this).prop('Counter', 0).animate({
    Counter: $(this).text() },
  {
    duration: 1500,
    easing: 'linear',
    step: function (now) {
      $(this).text(Math.ceil(now));
    } });

});
//# sourceURL=pen.js
    </script>





</body></html>