<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Web GIS 4G</title>
<!-- Favicon-->
<link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
<!-- Font Awesome icons (free version)-->
<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
<!-- Google fonts-->
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
<!-- Core theme CSS (includes Bootstrap)-->
<link href="css/styles.css" rel="stylesheet" />
<style>
    .map {
        width: 100%;
      }
	  .DlgTool{
	    z-index:1;
	    position:absolute;
	  }
	  
    </style>
<link href="dist/ol.css" rel="stylesheet">
<script src="dist/ol.js"></script>
<script src="dist/proj4js/proj4.js"></script>
<script src="dist/proj4js/proj4-src.js"></script>

<script src="js/jquery.js"></script>
<script src="js/jquery.wallform.js"></script>

</head>
<body>
  <div class="container-fluid">
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="#">WEB GIS 4G</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarText">
		<ul class="navbar-nav mr-auto">
		  <li class="nav-item active">
			<a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#">Features</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#">Pricing</a>
		  </li>
		</ul>
		<p class="text-right">
		  Navbar text with an inline element
		</p>
	  </div>
	</nav>

	<div id="map" class="map"></div>
	<div id="KooInfo" style="z-index:0; top:60px; left:200px; position:absolute; color:white;">Koordinat</div>
	<div id="Menu"  class="DlgTool" style="bottom:10px; left:300px;">
		<button type="button" class="btn btn-outline-success" onclick="Hs_Basemap()">Basemap</button>
		<button type="button" class="btn btn-outline-danger" onclick="Hs_layer()">Layer</button>
		<button type="button" class="btn btn-outline-warning">Analisa</button>
	</div>
	
	<div id="BoxtreeLayer" class="card DlgTool" style="width: 18rem; left:20px; top:150px; display:block;">
	  <div class="card-header">	Daftar layer </div>
	  <ul id="LayerTree" class="list-group list-group-flush"></ul>
	</div>

  <div id="BoxBasemap" class="card DlgTool col-4" style="width: 27rem; left:300px; bottom:150px; display:block;">
	  <div class="card-header">	Peta Dasar </div>
	  <div id="BasemapLayers" class="list-group list-group-flush" style="display:inline; max-height:150px; overflow:auto;"></div>
	</div>
	
</div>
<script>
var BasemapSource, BasemapLayer;
document.getElementById("map").style.height = (screen.height - 180) +'px';

BasemapSource = new ol.source.XYZ({
            url: 'https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}',
            });
 
 BasemapLayer =  new ol.layer.Tile({
                    source: BasemapSource,
                    nama : 'Peta Dasar'
                });

 var Layer1 = new ol.layer.Tile({
                nama : 'Jalan',
                source: new ol.source.TileWMS({
                url: 'http://localhost:8080/geoserver/wms?',
                params: {'LAYERS': 'SIG4G:JALAN_LN', 'TILED': true },
                serverType: 'geoserver',
            }) 
        }); 

    var Layer2 = new ol.layer.Tile({
        nama : 'Bangunan',
        source: new ol.source.TileWMS({
        url: 'http://localhost:8080/geoserver/wms?',
        params: {'LAYERS': 'SIG4G:BANGUNAN_PT', 'TILED': true },
        serverType: 'geoserver',
        }) 
    }); 

    var Layer3 = new ol.layer.Tile({
        nama : 'Batas Administrasi',
        source: new ol.source.TileWMS({
        url: 'https://geoservice.kalselprov.go.id/geoserver/BIROPEMOTDA/wms?',
        params: {'LAYERS': 'BIROPEMOTDA:PROVINSI_ADMINISTRASI_LN_50K', 'TILED': true },
        serverType: 'geoserver',
        }) 
    }); 


var layers =[BasemapLayer,Layer1,Layer2,Layer3]

var map = new ol.Map({
  layers: layers,
  target: 'map',
  view: new ol.View({
    center: ol.proj.fromLonLat([115.4000, -2.295333]),
    zoom: 13,
  }),
});

map.on ('pointermove', function(event){
   //document.getElementById("KooInfo").innerHTML = event.coordinate;
   coord3857 = event.coordinate;
   var coord4326 = ol.proj.transform(coord3857,'EPSG:3857','EPSG:4326');
   document.getElementById("KooInfo").innerHTML = ol.coordinate.toStringHDMS(coord4326);
}); 


function LoadDaftarLayer(){
  var n =0;
  map.getLayers().forEach(function(Layer){
      var Ly = '<li class="list-group-item" id="layer'+n+'_LI"><input type="checkbox" id="layer'+n+'_VBS" > '+Layer.get("nama")+'opacity <input id="layer'+n+'_OPC" class="opacity" type="range" min="0" max="1" step="0.01"/></li>';
	  
	  document.getElementById("LayerTree").innerHTML = Ly + document.getElementById("LayerTree").innerHTML 
	  n++;
  });
}

LoadDaftarLayer();

function bindInputs(layerid, layer) {
  var visibilityInput = $(layerid + '_VBS');
  visibilityInput.on('change', function () {
    layer.setVisible(this.checked);
  });
  visibilityInput.prop('checked', layer.getVisible());

  var opacityInput = $(layerid + '_OPC');
  opacityInput.on('input', function () {
    layer.setOpacity(parseFloat(this.value));
  });
  opacityInput.val(String(layer.getOpacity()));
}


function setup(id, group) {
  group.getLayers().forEach(function (layer, i) {
    var layerid = id + i;
    bindInputs(layerid, layer);
    if (layer instanceof ol.layer.Group) {
      setup(layerid, layer);
    }
  });
}
setup('#layer', map.getLayerGroup());


function LoadPetaDasar(){
  $.ajax({
    url: "/WebGIS4G/json/basemaps.json",
    cache: false,
    async: false,
    success: function(msg){
		  var s = "";
		  var n =0;
		  for (var key in msg) {
			   var baseURL = "'"+msg[n]['url']+"'";
			  	s = s + '<img src="/WebGIS4G/assets/basemap/'+msg[n]["images"]+'" title="'+msg[n]["name"]+
                  '" height="100" width="120" class="img-thumbnail" onclick="GantiPetaDasar('+baseURL+')" />';
			  n++;
		  }
		  $("#BasemapLayers").html(s);	
		}
  });	
};
LoadPetaDasar();

function GantiPetaDasar(n){
  BasemapSource = new ol.source.XYZ({
            url:n,
        });
  BasemapLayer.setSource(BasemapSource);
};

function Hs_layer(){
if ( document.getElementById("BoxtreeLayer").style.display == "none"){
		document.getElementById("BoxtreeLayer").style.display = "block";
	}else{
		document.getElementById("BoxtreeLayer").style.display = "none"
 }
}

function Hs_Basemap(){
if (document.getElementById("BoxBasemap").style.display == "none"){
		document.getElementById("BoxBasemap").style.display = "block";
	}else{
		document.getElementById("BoxBasemap").style.display = "none"
 }
}



</script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
</body>
</html>
