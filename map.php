<!DOCTYPE html>
<html>
  <head>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.37.0/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.37.0/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.1.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.1.0/mapbox-gl-geocoder.css' type='text/css' />
    <link href="css/fine-uploader-gallery.min.css" rel="stylesheet">
    <script src="scripts/fine-uploader.min.js"></script>
    <script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>
    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="Drop file here">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
			            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="qq-upload-button-selector qq-upload-button" style='width:200px;'>
                <div>Upload village picture</div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <div class="qq-thumbnail-wrapper">
                        <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
                    </div>
                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                    <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                        Retry
                    </button>

                    <div class="qq-file-info">
                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                        </button>
                    </div>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">No</button>
                    <button type="button" class="qq-ok-button-selector">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cancel</button>
                    <button type="button" class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>
    <style>
        body { margin:0; padding:0; }
        #map { position:absolute; top:0; bottom:0; width:100%; z-index:0;}
       
       #topBar {
       	position:fixed;
       	z-index:1;
       	background-color: #3A4E47;
       	width: 100%;
       	height: 35px;
       }
       
       #mapScreenDiv {
      	height:100vh;
      	position:relative;
       }
       
       #mapContainer {
       	position:relative;
       	padding:0px;
       	width:100%;
       	height:calc(100% - 30vh);
       }
       
       #projectScroller {
       	position:relative;
       	width:100%;
       	overflow-x:scroll;
       	white-space:nowrap;
       	background:white;
       }
       
       .hide-scrollbar ::-webkit-scrollbar-thumb{
 		   visibility : hidden;
		}
       
       #modalBlock {
       	position:fixed;
       	width:100%;
       	height:100%;
       	background-color:#888888;
       	opacity:.7;
       	display:none;
       	z-index:3;
       } 
       
       #drawer {
       	position:fixed;
       	overflow-y:scroll;
       	bottom:-5px;
       	height:0px;
       	background-color:white;
       	padding-left:10px;
       	padding-right:10px;
       	z-index:4;
       }
       
       .marker {
		    width: 32px;
   	 		height: 37px;
		    cursor: pointer;
		}
		       
       div.projectCell { 
       	display:inline-block;
       	/*border-left-width:1px;
       	border-right-width:1px;
       	border-top-width:2px;
       	border-bottom-width:2px;
       	border-style:solid;
       	padding-bottom:5px;*/
       	width:30vh;
       	margin-right:2px;
       	text-align:center;
       	cursor:pointer;
       	height:30vh;
       	background-size:30vh 30vh;
       	background-repeat:no-repeat;
       	-webkit-filter: contrast(1);
  		filter: contrast(1);
  		border:2px solid black;
  		border-radius:15px;
  		-moz-border-radius:15px;
       }
       
       div.expandoCell {
       	width:calc(99vw - 30vh);
       	height:calc(30vh - 10px);
       	margin-left:calc(30vh + 5px);
       	margin-right: 5px;
       	margin-top: 5px;
       	margin-bottom: 5px;
       	overflow-y:scroll;
       	white-space:normal;
       	vertical-align:top;
       }
       
       div.pictureScroller {
       	margin:0px;
       	padding:0px;
       	height:0px;
       	width:100%:
       	overflow-x:scroll;
       	overflow-y:hidden;
       	white-space:nowrap;
       }
       
       div.highlighted {
       	border-left-width:1px;
       	border-right-width:1px;
       	border-top-width:2px;
       	border-bottom-width:2px;
  		border-color: rgb(86, 180, 239);
		}
       
       div.progressBar {
      	position:relative;
       	height: 18px;
       	width:100%;
       	border:2px;
    	margin-left:0px;
    	margin-right:0px;
      }
      
      div.progressBar-label {
      	position:absolute;
      	font-size:12px;
      	text-align:center;
      	width:100%;
      	top:-1px;
      	font-weight: bold;
      }
      
      div.progressBar .ui-progressbar-value {
       	background-color:#8ABC5C;
       	padding:0px;
       	margin:0px;
  		}
  		
  	  input[type=button] {
  	  	cursor:pointer;
  	  	border: 0px;
  	  	-moz-radius:0px;
  	  	background-color:#8ABC5C;
  	  	color:white;
  	  }
  	  input[type=button].inactive {
  	  	cursor:default;
  	  	background-color:white;
  	  	color: black;
  	  	border: 1px solid #8ABC5C;
  	  }	
  	  
  	  #imagelightbox {
		position: fixed;
		z-index: 9999;
		 
		-ms-touch-action: none;
		touch-action: none;
	  }
	  
	  .mapboxgl-ctrl-geocoder {
  		font:15px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
  		position:relative;
  		background-color:white;
  		width:400px;
  		z-index:1;
  		border-radius:3px;
  	}
  
	  .mapboxgl-ctrl-geocoder input[type='text'] {
		  font-size:12px;
		  width:100%;
		  border:0;
		  background-color:transparent;
		  height:20px;
		  margin:0;
		  color:rgba(0,0,0,.5);
		  padding:10px 10px 10px 40px;
		  text-overflow:ellipsis;
		  white-space:nowrap;
		  overflow:hidden;
		}
		
		.coordinates {
		    background: rgba(0,0,0,0.5);
		    color: #fff;
		    position: absolute;
		    bottom: 10px;
		    left: 10px;
		    padding:5px 10px;
		    margin: 0;
		    font-size: 11px;
		    line-height: 18px;
		    border-radius: 3px;
		    display: none;
		}

    </style>  
     <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <link rel="stylesheet" href="css/lightbox.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
  <script>
  function scrollToAnchor(aid){
	    var aTag = $("a[name='"+ aid +"']");
	    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
	}

  </script>
  <script src="scripts/imagelightbox.min.js"></script>
   </head>
  	<body>
  	
  	<style>
		
		.map {
		    position: absolute;
		    top: 0;
		    bottom: 0;
		    width: 100%;
		}
	</style>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-compare/v0.1.0/mapbox-gl-compare.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-compare/v0.1.0/mapbox-gl-compare.css' type='text/css' />
  	<a name='mapAnchor' style='height:0px;padding:0px;margin:0px;' />
	<div id='modalBlock'></div>
	<div id='mapScreenDiv'>
	  	<div id='mapContainer'>
		  	<div class="fixed-action-btn" id='buttonHolder' style='position:absolute;bottom:20px;right:5px;'>
			    <a class="btn-floating btn-large yellow darken-1" id='countrySelectButton' onclick="zoomToCountryOverview();">
			      <i class="large material-icons">public</i>
			    </a>
			    <a class="btn-floating btn-large blue" id='addProjectButton' onclick="addAutocomplete();" style='margin-left:10px;' >
			      <i class="large material-icons">add_location</i>
			    </a>
	  		</div>
	 	    <div id='pictureDiv' class='pictureScroller' style='z-index:1000;position:absolute;bottom:0px;left:0px;right:0px;display:none;'></div>
	   	</div>
	   	
		<pre id='coordinates' class='coordinates'></pre>
	    <div id="projectScroller" class='hide-scrollbar'></div>
	    <div id="drawer">
	        <h3 id='projectTitle'></h3>
            <div style='text-align:center;width:100%;'>
            	<div id='progressbarProjectDetails' class='progressBar'></div>
            </div>
            <table style='width:100%;'><tr><td id='projectPercentageFunded'></td><td style='text-align:right;' id='projectGoal'></td></tr></table>
         
            
            <p id='projectSummary'></p>
            <input type='button' style='width:100%;height:30px;font-size:16px;margin-bottom:20px;' value='Donate Now!' />
	    </div>
	</div>
    <script>

    /*
    var applicationId = ''; // <-- Add your application's ID here

    // Initializes the payment form. See the documentation for descriptions of
    // each of these parameters.
    var paymentForm = new SqPaymentForm({
      applicationId: applicationId,
      inputClass: 'sq-input',
      inputStyles: [
        {
          fontSize: '15px'
        }
      ],
      cardNumber: {
        elementId: 'sq-card-number',
        placeholder: '•••• •••• •••• ••••'
      },
      cvv: {
        elementId: 'sq-cvv',
        placeholder: 'CVV'
      },
      expirationDate: {
        elementId: 'sq-expiration-date',
        placeholder: 'MM/YY'
      },
      postalCode: {
        elementId: 'sq-postal-code'
      },
      callbacks: {

        // Called when the SqPaymentForm completes a request to generate a card
        // nonce, even if the request failed because of an error.
        cardNonceResponseReceived: function(errors, nonce, cardData) {
          if (errors) {
            console.log("Encountered errors:");

            // This logs all errors encountered during nonce generation to the
            // Javascript console.
            errors.forEach(function(error) {
              console.log('  ' + error.message);
            });

          // No errors occurred. Extract the card nonce.
          } else {

            // Delete this line and uncomment the lines below when you're ready
            // to start submitting nonces to your server.
            alert('Nonce received: ' + nonce);


            /*
              These lines assign the generated card nonce to a hidden input
              field, then submit that field to your server.
              Uncomment them when you're ready to test out submitting nonces.

              You'll also need to set the action attribute of the form element
              at the bottom of this sample, to correspond to the URL you want to
              submit the nonce to.
            */
            // document.getElementById('card-nonce').value = nonce;
            // document.getElementById('nonce-form').submit();

/*          }
        },

        unsupportedBrowserDetected: function() {
          // Fill in this callback to alert buyers when their browser is not supported.
        },

        // Fill in these cases to respond to various events that can occur while a
        // buyer is using the payment form.
        inputEventReceived: function(inputEvent) {
          switch (inputEvent.eventType) {
            case 'focusClassAdded':
              // Handle as desired
              break;
            case 'focusClassRemoved':
              // Handle as desired
              break;
            case 'errorClassAdded':
              // Handle as desired
              break;
            case 'errorClassRemoved':
              // Handle as desired
              break;
            case 'cardBrandChanged':
              // Handle as desired
              break;
            case 'postalCodeChanged':
              // Handle as desired
              break;
          }
        },

        paymentFormLoaded: function() {
          // Fill in this callback to perform actions after the payment form is
          // done loading (such as setting the postal code field programmatically).
          // paymentForm.setPostalCode('94103');
        }
      }
    });

    // This function is called when a buyer clicks the Submit button on the webpage
    // to charge their card.
    function requestCardNonce(event) {

      // This prevents the Submit button from submitting its associated form.
      // Instead, clicking the Submit button should tell the SqPaymentForm to generate
      // a card nonce, which the next line does.
      event.preventDefault();

      paymentForm.requestCardNonce();
    }*/
    
    
    mapboxgl.accessToken = 'pk.eyJ1IjoiamRlcHJlZSIsImEiOiJNWVlaSFBBIn0.IxSUmobvVT64zDgEY9GllQ';
		var selectedCell, expandoCell, selectedElem, donateButton, oldBounds, addedMarkers = new Array(), geocoder = 0, isDragging, isCursorOverPoint, canvas, geojson;

		var coordinates = document.getElementById('coordinates');
		var map = new mapboxgl.Map({
		    container: 'mapContainer',
		    style: 'mapbox://styles/mapbox/outdoors-v10',
		    center: [32.807201, -16.931801],
		    zoom: 8
		});

		map.addControl(new mapboxgl.NavigationControl(), 'top-left');
		map.scrollZoom.disable();
		
       loadProjects(-16.931801, 32.807201, -9.493540, 35.662498);
       
       $.getJSON( "getCountriesJson.php", function( data ) {
	    	  countries = data;
	    	  /*for (i = 0; i < countries.length; i++) {
		    	  if (countries[i].country_code == 'gh') {
	    	  		loadProjects(countries[i].country_bounds_sw_lat, countries[i].country_bounds_sw_lng, countries[i].country_bounds_ne_lat, countries[i].country_bounds_ne_lng);
		    	  }
	    	  }*/
       });

	  function showDrawerAddNew(elem) {
	  		clearMarkers();
	    	$("#projectScroller").empty();
	    	console.log(elem);

	    	result = elem.result;
	    	canvas = map.getCanvasContainer();
    		geojson = {
    			    "type": "FeatureCollection",
    			    "features": [{
    			        "type": "Feature",
    			        "geometry": {
    			            "type": "Point",
    			            "coordinates": result.center
    			        }
    			    }]
    			};
	    					    	
    		map.addSource('villagePoint', {
    	        "type": "geojson",
    	        "data": geojson
    	    });
	    	    
		    map.addLayer({
		        "id": "point",
		        "type": "circle",
		        "source": "villagePoint",
		        "paint": {
		            "circle-radius": 10,
		            "circle-color": "#3887be"
		        }
		    });

		    map.on('mouseenter', 'point', function() {
		        map.setPaintProperty('point', 'circle-color', '#3bb2d0');
		        canvas.style.cursor = 'move';
		        isCursorOverPoint = true;
		        map.dragPan.disable();
		    });

		    map.on('mouseleave', 'point', function() {
		        map.setPaintProperty('point', 'circle-color', '#3887be');
		        canvas.style.cursor = '';
		        isCursorOverPoint = false;
		        map.dragPan.enable();
		    });

		    map.on('mousedown', mouseDown);
	    	
	    	$( "#projectScroller" ).html("<div style='margin:auto;max-width:600px;border-width:2px;border-style:solid;padding:10px;margin-top:10px;'><TABLE><TR><TD><H5>Add Your Village</H5>"
	    	    + "<input type='text' placeholder='Name of Your Town/Village' value='" + result.text + "' />"
				+ "<BR><input type='text' id='contactName' placeholder='Your Name' style='width:48%;' />"
				+ "<input type='text' id='contactEmail' placeholder='Your Email' style='margin-left:10px;width:48%;' />"
				+ "<BR><TEXTAREA id='howConnected' style='height:75px;width:100%;' placeholder=\"What's your connection to this place?\"></TEXTAREA>"
				+ "</TD><TD><div style='text-align:right;'><div id='uploader' style='margin-left:10px;'></div>"
				+ "<input type='button' value='Put it on the Map!' style='padding-left:40px;padding-right:40px;padding-top:10px;padding-bottom:10px;margin-top:10px;' /></div>"
				+ "</TD></TR></TABLE></div>");
	    	var uploader = new qq.FineUploader({
	            element: document.getElementById("uploader"),
	            scaling: {
	                sizes: [
	                    {name: "medium", maxSize: 800}
	                ], 
	                multiple: false
	            }
	        })
	  }
       
      function showDrawer(elem) {
          console.log("Showing drawer");
          $( "#pictureDiv" ).empty();
          var updatePictures = 0;
          if (elem.updatePictures) {
		  	updatePictures = elem.updatePictures.split("~");
			  for (i = 0; i < updatePictures.length; i++) {
				  breakPoint = updatePictures[i].indexOf(':');
				  imageId = updatePictures[i].substring(0, breakPoint);
				  description = updatePictures[i].substring(breakPoint + 1);
				  $( "#pictureDiv" ).append("<a href='uploads/thumb_" + imageId + "_default_see_800x600.jpeg' data-imagelightbox='d'><img style='height:25vh;' src='uploads/thumb_" + imageId + "_default_see_800x600.jpeg' alt=\"" + description + "\" /></a>");
  					var instanceD = $( 'a[data-imagelightbox="d"]' ).imageLightbox(
        				{
        					onLoadStart: function() { captionOff(); activityIndicatorOn(); },
        					onLoadEnd:	 function() { captionOn(); activityIndicatorOff(); },
        					onEnd:		 function() { captionOff(); activityIndicatorOff(); }
        				});
			  }
          }
          if (updatePictures && updatePictures.length > 0) {
              $('#projectTitle').css('margin-top','26vh');
          } else {
              $('#projectTitle').css('margin-top','1vh');
          }
          
          if (elem.updateDescriptions) {
		  	updateDescriptions = elem.updateDescriptions.split("~");
          }
          document.getElementById('projectTitle').innerHTML = elem.project_name + " (" + elem.village_name + ")";
          getProgressBar("ProjectDetails", elem.project_funded, elem.project_budget);
          document.getElementById('projectPercentageFunded').innerHTML = Math.floor(100 * elem.project_funded/elem.project_budget) + "% funded";
          document.getElementById('projectGoal').innerHTML = "Goal: $" + elem.project_budget;
          document.getElementById('projectSummary').innerHTML = elem.project_summary;
          
          $('#modalBlock').show();
    	  $('#drawer').show();
    	  $('#drawer').height="0px";
  	      $('#drawer').animate({height: "50vh"},'slow');
      }

      function hideDrawer() {
          console.log("Hiding drawer");
    	  $('#modalBlock').hide();
    	  $('#drawer').animate({height: "0px"},'slow', function() {
			$('#drawer').hide();
    	  });
    	  if (oldBounds) {
    			map.fitBounds(oldBounds);
    	  }
      }

      function addAutocomplete() {
          if (geocoder) {
              return;
          }
        $("#buttonHolder").hide(); 
     	  map.addControl(geocoder = new MapboxGeocoder({
    		    accessToken: mapboxgl.accessToken,
    		    placeholder: "Where is your village?"
    		}), 'bottom-right');

    	drawerShown = false;
  		geocoder.on('result', function(result) {
  	  		if (drawerShown) {
  	  	  		return;
  	  		}
  	  		drawerShown = true;
  	  		showDrawerAddNew(result);
  	  		map.removeControl(geocoder);
  	        $("#buttonHolder").show(); 
  		});
      }

      function clearMarkers() {
        if (addedMarkers.length == 0) {
            return;
        }
    	for (i = addedMarkers.length - 1; i >= 0; i--) {
   		     addedMarkers[i].remove();
   		     addedMarkers[i] = null;
   		}
   		addedMarkers = new Array();
      }

      function zoomToCountryOverview() {
  		clearMarkers();
        $("#projectScroller").empty();
		var bounds = new mapboxgl.LngLatBounds();
		for (i = 0;  i < countries.length; i++) {
			var el = document.createElement('div');
			el.className = 'marker';
			el.style.backgroundImage = "url(images/flag_" + countries[i].country_code + ".png)";
		    el.style.width = '48px';
		    el.style.height = '48px';
			marker = new mapboxgl.Marker(el, {offset:[-16, -16]})
			    .setLngLat([countries[i].country_longitude, countries[i].country_latitude])
			    .addTo(map);
		    bounds.extend([countries[i].country_longitude, countries[i].country_latitude]);
		    addedMarkers.push(marker);
			el.addEventListener('click', function (index) { return function(e) {
				loadProjects(countries[index].country_bounds_sw_lat, countries[index].country_bounds_sw_lng,
						countries[index].country_bounds_ne_lat, countries[index].country_bounds_ne_lng);
			}}(i));

			$( "#projectScroller" ).append( "<div id='countryDiv" + countries[i].country_id + "' class='projectCell'"
					+ "onclick=\"loadProjects(" + countries[i].country_bounds_sw_lat + ", " 
					+ countries[i].country_bounds_sw_lng + ", " + countries[i].country_bounds_ne_lat + ", "
					+ countries[i].country_bounds_ne_lng + ");\" ><img class='projectThumb' src='uploads/" 
    				+ countries[i].picture_filename + "' /></div>");
    				/*
    				+ "<BR><B>" + countries[i].country_label + "</B><BR><B>" 
    				+ (countries[i].projectCount > 0 ? countries[i].projectCount + "</B> project" + (countries[i].projectCount > 1 ? "s" : "") 
    				+ " in <B>" + countries[i].villageCount + "</B> village" + (countries[i].villageCount > 1 ? "s" : "") 
    				+ (countries[i].fundingCount > 0 ? "<BR><B>" + countries[i].fundingCount + "</B> " 
    	    				+ "project" + (countries[i].fundingCount > 1 ? "s" : "") + " in need of funding" : "<BR> -------------- ")
    				: countries[i].villageCount + "</B> proposed village" + (countries[i].villageCount > 1 ? "s" : "") + "<BR> -------------- </div>"));
			*/
		}
		map.fitBounds(bounds, {padding: 50});
      }
      
      function loadProjects(swLat, swLng, neLat, neLng) {
    	  $.getJSON( "getProjectsJson.php?swLat=" + swLat + "&swLng=" 
    	    	  + swLng + "&neLat=" + neLat + "&neLng=" + neLng, function( data ) {
    		  var bounds = new mapboxgl.LngLatBounds();
              clearMarkers();
              $("#projectScroller").empty();
             
    		  $.each( data, function(i, elem) {
    				$( "#projectScroller" ).append( "<div id='projectDiv" + elem.project_id + "' class='projectCell' style=\"position:relative;background-image:url('uploads/" 
    	    				+ elem.picture_filename + "');\">" 
    	    				+ "<div id='progressbar" + elem.project_id + "' class='progressBar' style='position:absolute;bottom:25px;left:0px;width:100%;'>"
    	    				+ "<div class='progressBar-label'>" + Math.floor(100 * elem.project_funded / elem.project_budget) 
    	    				+ "% of $" + elem.project_budget + "</div></div>"
    	    				+ "<input id='donateButton" + elem.project_id + "' type='button' style='position:absolute;border-radius:0px 0px 15px 15px;bottom:0px;left:0px;right:0px;height:25px;width:100%;text-align:center;font-size:14px;color:black;font-weight:bold;' value='" + elem.project_name + "' />"
							+ "</div>");
    	    				/*+ "<BR><B>" + elem.project_name + "</B><BR>" 
    	    				+ "<div id='progressbar" + elem.project_id + "' class='progressBar' >"
    	    				+ "<div class='progressBar-label'>" + Math.floor(100 * elem.project_funded / elem.project_budget) 
    	    				+ "% of $" + elem.project_budget + "</div></div>");*/

    				getProgressBar(elem.project_id, elem.project_funded, elem.project_budget);
					pos = [parseFloat(elem.project_lng), parseFloat(elem.project_lat)]

					var el = document.createElement('div');
					el.className = 'marker';
					el.style.backgroundImage = "url(images/type_" + elem.project_type + ".png)";
					 el.style.width = '64px';
				    el.style.height = '64px';
				    el.style.backgroundRepeat = "no-repeat";
					marker = new mapboxgl.Marker(el, {offset:[-32, -32]})
					    .setLngLat(pos)
					    .addTo(map);
					addedMarkers.push(marker);
				    
					el.addEventListener('click', function(e) {
						oldBounds = map.getBounds();
					  map.flyTo({
						    center: [elem.project_lng, elem.project_lat],
						    zoom: 16
						  });
					  
				      if (selectedCell) {
				      	$("#projectScroller").stop();
				      }
			          selected = $("#projectDiv" + elem.project_id);
			          halfWidth = 125;
			          $("#projectScroller").animate({
			        	  scrollLeft: document.getElementById("projectDiv" + elem.project_id).offsetLeft 
			        	  		+ halfWidth - Math.round($("#projectScroller").width() / 2)
			        	}, 500, function() {
			        		$("#projectDiv" + elem.project_id).animate({ width: '100%' }, 500);
							//showDrawer(elem);
			        	});
			        });
			        $("#projectDiv" + elem.project_id).on('click', function() {
				        if (selectedElem == elem) {
					        return;
				        }
						oldBounds = map.getBounds();
						  map.flyTo({
							    center: [elem.project_lng, elem.project_lat],
							    zoom: 16
							  });
						  if (selectedCell) {
							hideCell(function() { expandCell(elem); });
						  } else {
							  expandCell(elem);
						  }
			        });
					bounds.extend(pos);
        		});
      			if (bounds.getEast() - bounds.getWest() > .1) { 
      				map.fitBounds(bounds, {padding: 50});
      			} else {
          			map.fitBounds(new mapboxgl.LngLatBounds(new mapboxgl.LngLat(swLng, swLat),
          					new mapboxgl.LngLat(neLng, neLat)));
      			}
    		});
      }

	  function hideCell(nextAction) {
			if (expandoCell) {
				expandoCell.remove();
				expandoCell = null;
			}
			if (nextAction) {
      			selectedCell.animate({ width: '30vh'}, 500, nextAction);
			} else {
				selectedCell.animate({ width: '30vh'}, 500);
			}
	  		selectedCell.css("cursor", "pointer");
      		selectedCell = null;
      		$("#pictureDiv").animate({height: "0vh"}, 500);

            donateButton = $("#donateButton" + selectedElem.project_id);
      		donateButton.prop('value', selectedElem.project_name);
            donateButton.css("backgroundColor", '#8ABC5C');
            selectedElem = null;
	  }
      
      function expandCell(elem) {
        selectedElem = elem;
        
    	selectedCell = $("#projectDiv" + elem.project_id);
  		selectedCell.css("left", "0px");
  		selectedCell.css("cursor", "default");

  		selectedCell.animate({ width: '100%'}, 500);
  		$("#projectScroller").animate({ scrollLeft: document.getElementById("projectDiv" + elem.project_id).offsetLeft });

		expandoCell = $("<div>", {"class": "expandoCell"});
		expandoCell.className = 'expandoCell'; 
			
  		pictureDiv = $("#pictureDiv");
          pictureDiv.empty();
          
  		var updatePictures = 0;
          if (elem.updatePictures) {
    		  	updatePictures = elem.updatePictures.split("~");
			  for (i = 0; i < updatePictures.length; i++) {
				  breakPoint = updatePictures[i].indexOf(':');
				  imageId = updatePictures[i].substring(0, breakPoint);
				  description = updatePictures[i].substring(breakPoint + 1);
				  pictureDiv.append("<a href='uploads/thumb_" + imageId + "_default_see_800x600.jpeg' data-imagelightbox='d'><img style='height:25vh;' src='uploads/thumb_" + imageId + "_default_see_800x600.jpeg' alt=\"" + description + "\" /></a>");
  					var instanceD = $( 'a[data-imagelightbox="d"]' ).imageLightbox(
        				{
        					onLoadStart: function() { captionOff(); activityIndicatorOn(); },
        					onLoadEnd:	 function() { captionOn(); activityIndicatorOff(); },
        					onEnd:		 function() { captionOff(); activityIndicatorOff(); }
        				});
			  }
          }
          pictureDiv.css("display", "block");
          pictureDiv.animate({height: "25vh"}, 500);

          donateButton = $("#donateButton" + elem.project_id);
          if (elem.project_funded < elem.project_budget) {
          	donateButton.prop('value', "Donate Now!");
          } else {
            donateButton.prop('value', "Fully funded!");
            donateButton.css("backgroundColor", '#f6fdef');
          }
          expandoCell.append("<div style='margin:5px;text-align:left;font-weight:bold;'>" + elem.project_name + ' in ' + elem.village_name + "</div>"
          		+ "<div style='margin:5px;margin-bottom:50px;text-align:left;'>" + elem.project_summary + "</div>"
          		+ "<img style='position:absolute;top:5px;right:5px;width:24px;height:24px;cursor:pointer;' src='images/close_button.png' onclick='hideCell();window.event.stopPropagation();' />");
			
		  selectedCell.append(expandoCell);
      }

      function mouseDown() {
    	    if (!isCursorOverPoint) return;
    	    isDragging = true;
    	    canvas.style.cursor = 'grab';
    	    map.on('mousemove', onMove);
    	    map.once('mouseup', onUp);
    	}

    	function onMove(e) {
    	    if (!isDragging) return;
    	    var coords = e.lngLat;
			canvas.style.cursor = 'grabbing';
			geojson.features[0].geometry.coordinates = [coords.lng, coords.lat];
    	    map.getSource('villagePoint').setData(geojson);
    	}

    	function onUp(e) {
    	    if (!isDragging) return;
    	    var coords = e.lngLat;
			coordinates.style.display = 'block';
    	    coordinates.innerHTML = 'Longitude: ' + coords.lng + '<br />Latitude: ' + coords.lat;
    	    canvas.style.cursor = '';
    	    isDragging = false;
			map.off('mousemove', onMove);
    	}

      $("#modalBlock").on('click', function(e) {
	        hideDrawer();
      });

          function getProgressBar(id, funded, total) {
    	    $( "#progressbar" + id).progressbar({
    	      value: Math.round(funded),
    	      max: Math.round(total)
    	    });
          }

        				var
        					// ACTIVITY INDICATOR

        					activityIndicatorOn = function()
        					{
        						$( '<div id="imagelightbox-loading"><div></div></div>' ).appendTo( 'body' );
        					},
        					activityIndicatorOff = function()
        					{
        						$( '#imagelightbox-loading' ).remove();
        					},


        					// OVERLAY

        					overlayOn = function()
        					{
        						$( '<div id="imagelightbox-overlay"></div>' ).appendTo( 'body' );
        					},
        					overlayOff = function()
        					{
        						$( '#imagelightbox-overlay' ).remove();
        					},

        					// CLOSE BUTTON

        					closeButtonOn = function( instance )
        					{
        						$( '<button type="button" id="imagelightbox-close" title="Close"></button>' ).appendTo( 'body' ).on( 'click touchend', function(){ $( this ).remove(); instance.quitImageLightbox(); return false; });
        					},
        					closeButtonOff = function()
        					{
        						$( '#imagelightbox-close' ).remove();
        					},


        					// CAPTION

        					captionOn = function()
        					{
        						var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"] img' ).attr( 'alt' );
        						if( description.length > 0 )
        							$( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
        					},
        					captionOff = function()
        					{
        						$( '#imagelightbox-caption' ).remove();
        					},


        					// NAVIGATION

        					navigationOn = function( instance, selector )
        					{
        						var images = $( selector );
        						if( images.length )
        						{
        							var nav = $( '<div id="imagelightbox-nav"></div>' );
        							for( var i = 0; i < images.length; i++ )
        								nav.append( '<button type="button"></button>' );

        							nav.appendTo( 'body' );
        							nav.on( 'click touchend', function(){ return false; });

        							var navItems = nav.find( 'button' );
        							navItems.on( 'click touchend', function()
        							{
        								var $this = $( this );
        								if( images.eq( $this.index() ).attr( 'href' ) != $( '#imagelightbox' ).attr( 'src' ) )
        									instance.switchImageLightbox( $this.index() );

        								navItems.removeClass( 'active' );
        								navItems.eq( $this.index() ).addClass( 'active' );

        								return false;
        							})
        							.on( 'touchend', function(){ return false; });
        						}
        					},
        					navigationUpdate = function( selector )
        					{
        						var items = $( '#imagelightbox-nav button' );
        						items.removeClass( 'active' );
        						items.eq( $( selector ).filter( '[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ).index( selector ) ).addClass( 'active' );
        					},
        					navigationOff = function()
        					{
        						$( '#imagelightbox-nav' ).remove();
        					},


        					// ARROWS

        					arrowsOn = function( instance, selector )
        					{
        						var $arrows = $( '<button type="button" class="imagelightbox-arrow imagelightbox-arrow-left"></button><button type="button" class="imagelightbox-arrow imagelightbox-arrow-right"></button>' );

        						$arrows.appendTo( 'body' );

        						$arrows.on( 'click touchend', function( e )
        						{
        							e.preventDefault();

        							var $this	= $( this ),
        								$target	= $( selector + '[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ),
        								index	= $target.index( selector );

        							if( $this.hasClass( 'imagelightbox-arrow-left' ) )
        							{
        								index = index - 1;
        								if( !$( selector ).eq( index ).length )
        									index = $( selector ).length;
        							}
        							else
        							{
        								index = index + 1;
        								if( !$( selector ).eq( index ).length )
        									index = 0;
        							}

        							instance.switchImageLightbox( index );
        							return false;
        						});
        					},
        					arrowsOff = function()
        					{
        						$( '.imagelightbox-arrow' ).remove();
        					};
    </script>
     </body>
</html>