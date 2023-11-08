<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="MapTiler AG">
    <meta name="keywords" content="world maps, maps, api, vector, raster, tiles">
    <meta name="description" content="World maps for web and mobile. Maps API and hosting for spatial data.">
    <meta property="og:image" content="https://media.maptiler.com/old/img/share/share-default.png">
    <meta property="og:url" content="https://www.maptiler.com/">

    <meta name="theme-color" content="#fff">

    <link rel="icon" sizes="32x32" type="image/png" href="/static/img/favicon/favicon.png?t=1677738994">
    <link rel="icon" sizes="192x192" href="/static/img/favicon/android-chrome.png?t=1677738994">
    <link rel="apple-touch-icon" sizes="180x180" href="/static/img/favicon/apple-touch-icon.png?t=1677738994">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;1,400&amp;family=Ubuntu:wght@400;500&amp;display=swap" rel="stylesheet">

    <link href="/static/css/cloud_base.css?t=1696921022" rel="stylesheet">
    <script async="" src="https://www.googletagmanager.com/gtm.js?id=GTM-K6SD2VP"></script><script src="/static/js/cloud_base.js?t=1698829670"></script>
    <script src="/static/js/bootstrap.bundle.min.js?t=1677738994"></script>

    <title>Data / BE_Building | MapTiler Cloud</title>


<link rel="stylesheet" type="text/css" href="https://cdn.maptiler.com/mapbox-gl-js/v1.13.2/mapbox-gl.css">
<script src="https://cdn.maptiler.com/mapbox-gl-js/v1.13.2/mapbox-gl.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>


</head>

  <body data-new-gr-c-s-check-loaded="14.1136.0" data-gr-ext-installed="" data-new-gr-c-s-loaded="14.1136.0">



    <header class="topbar topbar-cloud px-3 px-lg-gutter text-white d-flex justify-content-between align-items-center">
        <a href="/">
          <svg class="text-white" width="230" height="55" viewBox="0 0 320 55">
            <use href="/static/img/logo/maptiler-logo-adaptive-cloud.svg#cloud"></use>
          </svg>
        </a>
        <button id="asideToggler" class="navbar-toggler d-block d-lg-none">

<svg class="p-0 scale-150 text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
  <use href="/static/img/icon/icon.svg#menu">
  </use>
</svg>

        </button>
      </header>
    <div class="mt-dashboard">
      <aside id="cloudAside" class="mt-aside">
          <nav class="cloudNav justify-content-between justify-content-lg-end align-items-center mb-2">
            <div class="product-switch-wrap order-2 order-lg-1 px-gutter px-lg-0">
              <a tabindex="0" type="button" class="product-switch">
                <svg class="p-0" version="1.1" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
                  <use href="/static/img/icon/icon.svg#apps"></use>
                </svg>
              </a>
            <div tabindex="0" class="product-switch-container">
              <h6 class="switch-title">Switch to</h6>
              <div>
                <a class="switch-link web-switch" href="https://www.maptiler.com/">Website</a>
                <a class="switch-link cloud-switch" href="/maps/">MapTiler Cloud</a>
                <a class="switch-link data-switch" href="https://data.maptiler.com/my-extracts/">MapTiler Data</a>
              </div>
            </div>
          </div>

            <div class="col-12 col-lg-7 order-1 order-lg-2">

                <p class="w-100 text-center cloud-name" title="reydencagata@gmail.com">Reyden Cagata</p>

            </div>

            <div class="order-3 order-lg-3 px-gutter px-lg-0">
              <a type="button" class="btn btn-primary btn-sm float-right" href="/auth/sign-out?next=https://www.maptiler.com/">Sign out</a>
            </div>
          </nav>


<a id="linkMaps" class="aside-link mt-lg-2  " href="/maps/">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#public"></use>
  </svg>

  Maps
</a>


<a id="linkTiles" class="aside-link  " href="/tiles/">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#tiles"></use>
  </svg>

  Tiles
</a>


<a id="linkData" class="aside-link  " href="/data/">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#pin_drop"></use>
  </svg>

  Data
</a>


<a id="linkAPI Keys" class="aside-link  " href="/account/keys/">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#vpn_key"></use>
  </svg>

  API Keys
</a>


<a id="linkAnalytics" class="aside-link  " href="/account/analytics">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#stacked_bar_chart"></use>
  </svg>

  Analytics
</a>


<a id="linkAccount" class="aside-link  " href="/account/settings">

  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <use href="/static/img/icon/icon.svg#settings1"></use>
  </svg>

  Account
</a>




      </aside>
      <section class="mt-container">
        <p class="mt-0 mb-1 px-sm-4 px-3 py-2 small font-weight-bolder text-secondary bg-lighter">
<a href="/data/">Data</a> /
<a href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/">BE_Building</a>



</p>
        <div class="mt-content m-0 px-sm-4 px-3">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">




<div class="row mt-1">
  <div class="col-sm-12">
    <div id="map-preview" class="map-preview border rounded border-lighter w-100 h-450px mapboxgl-map">
      <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
    <div class="mapboxgl-canary" style="visibility: hidden;"></div><div class="mapboxgl-canvas-container mapboxgl-interactive mapboxgl-touch-drag-pan mapboxgl-touch-zoom-rotate"><canvas class="mapboxgl-canvas" tabindex="0" aria-label="Map" role="region" width="1336" height="673" style="width: 891px; height: 449px;"></canvas></div><div class="mapboxgl-control-container"><div class="mapboxgl-ctrl-top-left"></div><div class="mapboxgl-ctrl-top-right"></div><div class="mapboxgl-ctrl-bottom-left"><div class="mapboxgl-ctrl" style="display: none;"><a class="mapboxgl-ctrl-logo" target="_blank" rel="noopener nofollow" href="https://www.mapbox.com/" aria-label="Mapbox logo"></a></div></div><div class="mapboxgl-ctrl-bottom-right"><div class="mapboxgl-ctrl mapboxgl-ctrl-attrib"><button class="mapboxgl-ctrl-attrib-button" title="Toggle attribution" aria-label="Toggle attribution"></button><div class="mapboxgl-ctrl-attrib-inner" role="list"><a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">© OpenStreetMap contributors</a></div></div></div></div></div>
  </div>


<div class="col-sm-12 text-end mt-1">

    <a class="btn btn-secondary btn-sm" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/publish">Publish</a>

  <a class="btn btn-primary btn-sm" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/edit">Edit dataset</a>

  <button class="btn btn-lighter p-o5 text-body" type="button" id="datasetMenuBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

<svg class="" version="1.1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
  <use href="/static/img/icon/icon.svg#more_vert">
  </use>
</svg>

  </button>
  <div class="dropdown-menu" aria-labelledby="datasetMenuBtn">
    <a class="dropdown-item" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/upload">Upload</a>
    <a class="dropdown-item" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/metadata">Edit metadata</a>
    <a class="dropdown-item" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/history">History</a>
    <a class="dropdown-item" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/delete">Delete</a>
  </div>
</div>








  <div class="col-sm-10">
    <h3>Use vector data</h3>
    <p>Public GeoJSON API endpoint for your apps.</p>


  <a id="copy-file_url-link" class="copy btn btn-lighter p-1" data-link="https://api.maptiler.com/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/features.json?key=aF7HhncV5bhT2pqqWdRV" onclick="window.open(this.dataset.link)" title="Open link">
<svg class="" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
  <use href="/static/img/icon/icon.svg#launch">
  </use>
</svg>
</a>

  <button type="button" id="copy-file_url-button" class="copy btn btn-lighter p-1" data-clipboard-text="https://api.maptiler.com/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/features.json?key=aF7HhncV5bhT2pqqWdRV" title="Copy link"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><use href="/static/img/icon/icon.svg#content_copy"></use></svg></button>
  <input id="copy-file_url-input" class="form-control w-60 d-inline-block" type="text" value="https://api.maptiler.com/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/features.json?key=aF7HhncV5bhT2pqqWdRV">
<br>
    <p>Prepared examples:</p>
    <ul>
      <li>
        <a target="_blank" href="https://docs.maptiler.com/maplibre-gl-js/geojson-polygon/?key=aF7HhncV5bhT2pqqWdRV&amp;dataId=c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a">
          JavaScript Maps API
        </a>
      </li>
      <li>
        <a target="_blank" href="https://docs.maptiler.com/openlayers/geojson-polygon/?key=aF7HhncV5bhT2pqqWdRV&amp;dataId=c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a">OpenLayers</a>
      </li>
    </ul>
  </div>

  <div class="col-sm-10">
    <h3>Download geodata</h3>
    <p>Export geometry as GeoJSON file.</p>
    <p><a class="btn btn-sm btn-primary" href="/data/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/download">Download</a>
  </p></div>


  <div class="col-sm-10">
    <h3 class="mt-3">Dataset details</h3>
      <p>
        <b>Number of features</b> 1<br>
        <b>Created</b>
<span class="local-datetime" data-utc="2023-11-08T03:21:17.127937+00:00" title="2023-11-08 03:21 UTC">2023-11-08 11:21</span>
<br>
        <b>Last modified</b>
<span class="local-datetime" data-utc="2023-11-08T04:24:49.050220+00:00" title="2023-11-08 04:24 UTC">2023-11-08 12:24</span>
<br>
      </p>



  </div>
</div>
<script>
var map = new mapboxgl.Map({
  container: 'map-preview',
  hash: true,
  style: 'https://api.maptiler.com/maps/streets/style.json?key=aF7HhncV5bhT2pqqWdRV'
});

fetch("/api/datasets/c9e16c1a-b819-4f4a-b8a0-ec65dc1b8f8a/file")
.then(function(response) {return response.json();})
.then(function(geojson) {
  map.on('load', function() {
    map.addSource('geojson-overlay', {
      'type': 'geojson',
      'data': geojson
    });
    map.addLayer({
      'id': 'geojson-overlay-fill',
      'type': 'fill',
      'source': 'geojson-overlay',
      'filter': ['==', '$type', 'Polygon'],
      'layout': {},
      'paint': {
          'fill-color': '#fff',
          'fill-opacity': 0.4
      }
    });
    map.addLayer({
      'id': 'geojson-overlay-line',
      'type': 'line',
      'source': 'geojson-overlay',
      'layout': {},
      'paint': {
          'line-color': 'rgb(68, 138, 255)',
          'line-width': 3
      }
    });
    map.addLayer({
      'id': 'geojson-overlay-point',
      'type': 'circle',
      'source': 'geojson-overlay',
      'filter': ['==', '$type', 'Point'],
      'layout': {},
      'paint': {
          'circle-color': 'rgb(68, 138, 255)',
          'circle-stroke-color': '#fff',
          'circle-stroke-width': 6,
          'circle-radius': 7
      }
    });

    var bounds = [Infinity, Infinity, -Infinity, -Infinity];

    var processCoordinates = function(coords) {
      if (Array.isArray(coords[0])) {
        coords.map(c => processCoordinates(c));
      } else {
        bounds[0] = Math.min(bounds[0], coords[0]);
        bounds[1] = Math.min(bounds[1], coords[1]);
        bounds[2] = Math.max(bounds[2], coords[0]);
        bounds[3] = Math.max(bounds[3], coords[1]);
      }
    };

    geojson.features.forEach(function(f) {
      if (f.geometry && f.geometry.coordinates) {
        processCoordinates(f.geometry.coordinates);
      }
    });

    map.fitBounds(bounds, {
      padding: 20
    });
  });
});
</script>

              </div>
            </div>
          </div>
        </div>
        <footer class="footer footer-dashboard my-2">
          <div class="row align-items-center h-100">
            <div class="col-12 text-end">
                <a class="text-body" href="https://www.maptiler.com">
                  ©&nbsp;2023&nbsp;MapTiler
                </a>
                <a class="text-body" href="https://www.maptiler.com/terms/">/ Terms</a>
                <a class="text-body" href="https://www.maptiler.com/privacy-policy/">/ Privacy</a>
            </div>
          </div>
        </footer>
      </section>
    </div>









<script>
  window.dataLayer = window.dataLayer || [];


    window.dataLayer.push({
        "userId": "15d51cb5-7878-41e4-b058-8d7ae285fe8c"
    });






(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K6SD2VP');



  var whenGALoaded = function() {
    var ga = window.ga;
    if (ga && ga.loaded && ga.getAll) {
      var instance = ga.getAll()[0]; // TODO
      if (instance) {
        var clientId = instance.get('clientId');
        if (clientId && clientId.length > 0) {
          postJson('/api/assign-google-analytics-identity', {
            'client_id': clientId
          });
        }
      }
    } else {
      setTimeout(whenGALoaded, 500);
    }
  };
  whenGALoaded();


</script>



<div id="volume-booster-visusalizer">
                    <div class="sound">
                        <div class="sound-icon"></div>
                        <div class="sound-wave sound-wave_one"></div>
                        <div class="sound-wave sound-wave_two"></div>
                        <div class="sound-wave sound-wave_three"></div>
                    </div>
                    <div class="segments-box">
                        <div data-range="1-20" class="segment"><span></span></div>
                        <div data-range="21-40" class="segment"><span></span></div>
                        <div data-range="41-60" class="segment"><span></span></div>
                        <div data-range="61-80" class="segment"><span></span></div>
                        <div data-range="81-100" class="segment"><span></span></div>
                    </div>
                </div><textarea readonly="" style="font-size: 12pt; border: 0px; padding: 0px; margin: 0px; position: absolute; left: -9999px; top: 382.667px;"></textarea></body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration></html>