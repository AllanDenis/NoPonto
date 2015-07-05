/**================================================
JS : CUSTOM SCRIPTS NOPONTO
===================================================*/

/* @@@@@@@@@@ BD NOPONTO DESIGN3 @@@@@@@@@@ */

var geocoder,
    map,
    marker,
    myLatlng,
    directionsDisplay,
    directionsService = new google.maps.DirectionsService();

// ################# INIT MAP #####################

function initialize() {
  directionsDisplay = new google.maps.DirectionsRenderer();
  var latlng = new google.maps.LatLng(-9.6651146, -35.7306113);
  var options = {
    zoom: 16,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById("mapa"), options);

  geocoder = new google.maps.Geocoder();

  marker = new google.maps.Marker({
    map: map,
    draggable: true,
  });

  marker.setPosition(latlng);
  directionsDisplay.setMap(map);
}

initialize();

// ################# CALC ROUTES #####################

function calcRoute() {
  var start = document.getElementById('start').value,
      end = document.getElementById('end').value;

  var request = {
      origin:start,
      destination:end,
      travelMode: google.maps.TravelMode.DRIVING
  };

  //remover rotas existentes
  if (directionsDisplay) {
    directionsDisplay.setMap(null);
  }

  if (start == "" || end == "") {
    document.getElementById("msgDiv").innerHTML = ''+
    '<div class="alert alert-danger">'+
      '<a href="#" class="close" data-dismiss="alert">&times;</a>'+
      '<strong>Erro!</strong> Entre com os dois campos preenchidos!'+
    '</div>'+
    '';
  } else {
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        this.directionsDisplay = new google.maps.DirectionsRenderer();
        this.directionsDisplay.setDirections(response);
        this.directionsDisplay.setMap(map);
        console.log(response);
      }
    });
  };
};

// ################# LINES #####################

function calcLines() {
  var line = document.getElementById('line').value,
      lineText = document.getElementById('line'),
      breakString = line.split("/"),
      startLine = breakString[0],
      endLine = breakString[1];

  var directionLine = lineText.options[lineText.selectedIndex].text;

  var request = {
        origin:startLine,
        destination:endLine,
        travelMode: google.maps.TravelMode.DRIVING
  };

  var polylineOptionsIda = new google.maps.Polyline({
        strokeColor: 'green',
        strokeOpacity: 1.0,
        strokeWeight: 10
  });

  var polylineOptionsVolta = new google.maps.Polyline({
        strokeColor: 'red',
        strokeOpacity: 1.0,
        strokeWeight: 10
  });

  //remover rotas existentes
  if (directionsDisplay) {
    directionsDisplay.setMap(null);
  }

  if (startLine == "" || endLine == "") {
    document.getElementById("msgDiv").innerHTML = ''+
    '<div class="alert alert-danger">'+
      '<a href="#" class="close" data-dismiss="alert">&times;</a>'+
      '<strong>Erro!</strong> Entre com os dois campos preenchidos!'+
    '</div>'+
    '';
  } else {
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        if (directionLine.match(/IDA/)) {
          this.directionsDisplay = new google.maps.DirectionsRenderer({polylineOptions: polylineOptionsIda});
          this.directionsDisplay.setMap(map);
          this.directionsDisplay.setDirections(response);
          console.log(response);
        }
        else {
          this.directionsDisplay = new google.maps.DirectionsRenderer({polylineOptions: polylineOptionsVolta});
          this.directionsDisplay.setMap(map);
          this.directionsDisplay.setDirections(response);
          console.log(response);
        }
      }
    });
  };
};

// ################# GET ADDRESS #####################

function getAddress() {

  var address = document.getElementById('txtEndereco').value;

  if (address === '') {
    alert('Entre com um local e clique em pesquisar...');
    return;
  }

  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {

      //Remove marcador existente
      if (marker) {
        marker.setMap(null);
      }

      map.setCenter(results[0].geometry.location);

      marker = new google.maps.Marker({
          map: map,
          zoom: 15,
          position: results[0].geometry.location
      });
    }
    else {
      alert('Não foi possível encontrar esse local, tente novamente...');
    }
  });
};

/// ################# AUTOCOMPLETE #####################

var searchBar = document.getElementById('txtEndereco');

var autocomplete = new google.maps.places.Autocomplete(searchBar);
autocomplete.bindTo('bounds', map);


google.maps.event.addListener(autocomplete, 'place_changed', function(event) {

  marker.setVisible(false);

  var place = autocomplete.getPlace();

  //Remove marcador existente
  if (marker) {
    marker.setMap(null);
  }

  var position = new google.maps.LatLng(place.geometry.location.A, place.geometry.location.F);


  marker = new google.maps.Marker({
    map: map,
    zoom: 25,
    position: position
  });

  map.setZoom(17);
  map.setCenter(marker.getPosition());

  marker.setVisible(true);
});


// ------------------- AUTOCOMPLETE START ----------------------
var searchBarA = document.getElementById('start');
console.log(searchBarA);

var autocompleteA = new google.maps.places.Autocomplete(searchBarA);
autocompleteA.bindTo('bounds', map);

// ------------------- AUTOCOMPLETE END ------------------------
var searchBarB = document.getElementById('end');

var autocompleteB = new google.maps.places.Autocomplete(searchBarB);
autocompleteB.bindTo('bounds', map);

// ################# Current Position #####################

function getCurrentLocation() {

    //Verificar HTML5 geoLocation
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        currentLoc = new google.maps.LatLng(lat, lng);

        //Remove marcador existente
        if (marker) {
          marker.setMap(null);
        }

        var popupContent = '<div id="content"><h5 id="firstHeading" class="heading">Sua localização!</h5></div>'

        var infowindow = new google.maps.InfoWindow({
          content: popupContent
        });

        map.setCenter(currentLoc);

        marker = new google.maps.Marker({
            map: map,
            zoom: 16,
            position: currentLoc
        });

        infowindow.open(map,marker);
      });

    }
    else {
      alert('Este browser não suporta HTML5 geolocation');
    }
  };


// ################# LOAD PONTOS #####################

function loadPontos() {

    $.ajax({
              //url : "../slim.test/index.php/pontos",
              //url : "../teste.php",
              url : "data/noponto-estaticos.json",
              //contentType: "application/json",
              dataType : "json",
              success : function(data){
                console.log("Sucesso!");
                console.log(data);

                map.data.loadGeoJson("data/noponto-estaticos.json");
                //map.data.loadGeoJson("data/noponto-estaticos.json");

                map.data.setStyle({
                  fillColor: 'blue',
                  position: myLatlng,
                  map: map,
                  title: 'location',
                  icon: 'images/bus2.png'
                });

                var infowindow = new google.maps.InfoWindow();

                map.data.addListener('click', function(event) {
                  infowindow.setContent("<div><h6>Ponto de ônibus</h6>"+
                                          '<div class="define-width-list"></div>'+
                                          '<div class="list-group">'+
                                            '<a href="#" class="list-group-item active">'+
                                              'Linhas'+
                                            '</a>'+
                                            '<div class="list-group-item">'+
                                              '<div class="status-bus-next">'+
                                                '<span><b>Próximo</b></span>'+
                                              '</div>'+
                                              '<span><b>- Feitosa X Centro / Farol</b></span>'+
                                            '</div>'+
                                            '<div class="list-group-item">'+
                                              '<div class="status-bus-min">'+
                                                '<span><b>12 min</b></span>'+
                                              '</div>'+
                                              '<span><b>- Jose Tenorio / Iguatemi</b></span>'+
                                            '</div>'+
                                            '<div class="list-group-item">'+
                                              '<div class="status-bus-min">'+
                                                '<span><b>20 min</b></span>'+
                                              '</div>'+
                                              '<span><b>- Sanatorio / Sinimbu</b></span>'+
                                            '</div>'+
                                          '</div>'+
                                        "</div>");
                  infowindow.setPosition(event.feature.getGeometry().get());
                  infowindow.open(map, this.marker);

                  $.each(data, function(key, val) {
                    //console.log(key);
                    console.log(val);
                  });

                  //console.log(map.data.properties.features.id);
                });

              },

              error: function(XMLHttpRequest, textStatus, errorThrown){
                console.log("Erro!");
                console.log(XMLHttpRequest);
                console.log(XMLHttpRequest.responseText);
                //document.write(XMLHttpRequest.responseText)
                //console.log(JSON.stringify(XMLHttpRequest, null, 4));
                console.log(textStatus);
                console.log(errorThrown);
              }

    });//ajax

}

loadPontos();
