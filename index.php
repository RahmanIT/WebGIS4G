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
	  
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 280px;
      }
      .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }
      .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
      }
      .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
      }
      .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
      }
      .ol-popup-closer:after {
        content: "✖";
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

  <div id="popup" class="ol-popup">
      <a href="#" id="popup-closer" class="ol-popup-closer"></a>
      <div id="popup-content"></div>
  </div>

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

  <div id="BoxBasemap" class="card DlgTool col-4" style="width: 27rem; display:none; left:300px; bottom:60px;">
	  <div class="card-header">	Peta Dasar </div>
	  <div id="BasemapLayers" class="list-group list-group-flush" style="display:inline; max-height:150px; overflow:auto;"></div>
	</div>
	
</div>
<script>
var BasemapSource, BasemapLayer;
var container = document.getElementById('popup');
var content = document.getElementById('popup-content');
var closer = document.getElementById('popup-closer');

/**
 * Create an overlay to anchor the popup to the map.
 */
var overlay = new ol.Overlay({
  element: container,
  autoPan: {
    animation: {
      duration: 250,
    },
  },
});

/**
 * Add a click handler to hide the popup.
 * @return {boolean} Don't follow the href.
 */
closer.onclick = function () {
  overlay.setPosition(undefined);
  closer.blur();
  return false;
};
/**
 * setting tinggi map sesuai screen layar.
 */
document.getElementById("map").style.height = (screen.height - 180) +'px';
/**
 * definis sumber pata dasar.
 */
BasemapSource = new ol.source.XYZ({
            url: 'https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}',
            });
 /**
 * definis varibael layer pata dasar.
 */
 BasemapLayer =  new ol.layer.Tile({
                    source: BasemapSource,
                    nama : 'Peta Dasar',
                    LayerType : "ESRI",
                });
 /**
 * definis varibael layer tematik.
 */
 var Layer1 = new ol.layer.Tile({
                nama : 'Jalan',
                LayerType : "OGC",
                source: new ol.source.TileWMS({
                url: 'http://localhost:8080/geoserver/wms?',
                params: {'LAYERS': 'SIG4G:JALAN_LN', 'TILED': true },
                serverType: 'geoserver',
            }) 
        }); 

    var Layer2 = new ol.layer.Tile({
        nama : 'Bangunan',
        LayerType : "OGC",
        source: new ol.source.TileWMS({
        url: 'http://localhost:8080/geoserver/wms?',
        params: {'LAYERS': 'SIG4G:BANGUNAN_PT', 'TILED': true },
        serverType: 'geoserver',
        }) 
    }); 

    var Layer3 = new ol.layer.Tile({
        nama : 'Batas Administrasi',
        LayerType : "OGC",
        source: new ol.source.TileWMS({
        url: 'https://geoservice.kalselprov.go.id/geoserver/wms?',
        params: {'LAYERS': 'BIROPEMOTDA:PROVINSI_ADMINISTRASI_LN_50K' },
        serverType: 'geoserver',
        }) 
    }); 

 /**
 * definis arry semua layers.
 */
var layers =[BasemapLayer,Layer1,Layer2,Layer3]
 /**
 * definis varibael utama Map.
 */
var map = new ol.Map({
  layers: layers,
  target: 'map',
  overlays: [overlay],
  view: new ol.View({
    center: ol.proj.fromLonLat([115.45770, -2.330333]),
    zoom: 15,
  }),
});
 /**
 * definis koordinat posisi korusur / even mouse move.
 */
map.on ('pointermove', function(event){
   coord3857 = event.coordinate;
   var coord4326 = ol.proj.transform(coord3857,'EPSG:3857','EPSG:4326');
   document.getElementById("KooInfo").innerHTML = ol.coordinate.toStringHDMS(coord4326);
}); 

/**
 * Add a click handler to the map to render the popup.
 * menampilkan data saat map di klik.
 */
map.on('singleclick', function (evt) {
  var coordinate = evt.coordinate;
  var coord4326 = ol.proj.transform(coordinate,'EPSG:3857','EPSG:4326');
  var hdms = ol.coordinate.toStringHDMS(coord4326);
  
  var view = map.getView();
  var viewResolution = view.getResolution();
  var loopingData = true;
  map.getLayers().forEach(function(layer){    
    if(loopingData == true){
      if(layer.get('visible') == true && layer.get('LayerType') == "OGC"){
        var url = layer.getSource().getFeatureInfoUrl(coordinate, viewResolution, view.getProjection(),{'INFO_FORMAT': 'application/json', 'FEATURE_COUNT':1});
        console.log(url);  
        if(url){
            var urld = encodeURIComponent(url);
            $.ajax({
							url: "proxy.php",
							data: "url="+urld,
							cache: false,
							async: false,
							success: function(msg){	
                        var hmlhpopup="";                        
                        try {                          
                          console.log(msg);
                          if(msg == "0"){
                              loopingData = true;
                            }else{
                              var data = JSON.parse(decodeURIComponent(msg));
                              infos = data['features'][0]['properties'];
                              for (var key in infos) {
                                var value = infos[key];
                                content_html = "<tr><td>" + key + "</td><td>" + value + "</td></tr>";
                                hmlhpopup = hmlhpopup + content_html;										
                              };
                              loopingData = false;
                            }
                          
                        } catch (error) {
                          //
                          loopingData = true;
                        };
                        content.innerHTML = '<code>' + hdms + '</code>'+
                        "<table class='table table-ligh table-striped table-bordered table-sm'><tbody>"+hmlhpopup+ "</tbody></table>" ;
                        overlay.setPosition(coordinate);			
								      } //end success
						});	 //end ajax
          }; // end jika variabel URL ada
      };  // end jikan layar aktif dan type nya adalah geoserver
    }; // jika perulangan layer TRUE / data layer tiadak di temukan
  }); // end looping layer list
});

 /**
 * definis struktru HTML layer tree/ Daftat Layer Panel.
 */
function LoadDaftarLayer(){
  var n =0;
  map.getLayers().forEach(function(Layer){
      var Ly = '<li class="list-group-item" id="layer'+n+'_LI"><input type="checkbox" id="layer'+n+'_VBS" > '+Layer.get("nama")+'opacity <input id="layer'+n+'_OPC" class="opacity" type="range" min="0" max="1" step="0.01"/></li>';
	  
	  document.getElementById("LayerTree").innerHTML = Ly + document.getElementById("LayerTree").innerHTML 
	  n++;
  });
}
LoadDaftarLayer();

 /**
 * defini fungsi objek layer opacity dan layer visbile.
 */
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

 /**
 * setup menghubungkan fungsi Objek HTML layer ke fungs Layers.Map.
 */
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

 /**
 * membuat menu daftat peta dasar dalam BOX basemaps.
 */
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
};

function Hs_Basemap(){
if (document.getElementById("BoxBasemap").style.display == "none"){
		document.getElementById("BoxBasemap").style.display = "block";
	}else{
		document.getElementById("BoxBasemap").style.display = "none"
 }
};
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
