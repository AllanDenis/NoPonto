/**================================================
JS : CUSTOM SCRIPTS
===================================================*/

var map, marker, myLatlng, mapOptions, geoCoder, currentLoc, searchBtn;

//Search component
var Searchbar = React.createClass({
  
  //Search click event
  getAddress: function(event) {
    
    //Get address from input
    var address = document.getElementById('search-input').value;

    //If no address is entered, display an alert and return;
    if (address === '') {
      alert('Entre com um local e clique em pesquisar...');
      return;
    }
      
    //Use address and add a marker to the searched address
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        
        //Remove previously added marker
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
  },

  //Current location click event
  getCurrentLocation: function() {
    
    //If brower supports HTML5 geoLocation
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) { 
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        currentLoc = new google.maps.LatLng(lat, lng);

        //Remove previously added marker
        if (marker) {
          marker.setMap(null);
        }

        var popupContent = '<div id="content"><h3 id="firstHeading" class="heading">Sua localização</h1></div>'

        var infowindow = new google.maps.InfoWindow({
          content: popupContent
        });

        map.setCenter(currentLoc);//Set the map to center of location

        marker = new google.maps.Marker({
            map: map,
            zoom: 15,
            position: currentLoc
        });

        infowindow.open(map,marker);
      });
        
    }
    else {
      alert('Este browser não suporta HTML5 geolocation');
    }
  },
  
  //Render search input, search btn, current location image
  render: function() {
    return (
      <div>
        <div className="bar-top"></div>
        <div className="container">
          <h1>
            <img src="images/noponto-logo.png" className="logo" />
          </h1>
  
          <div className="input-group margin-board">
                <input type="text" id="search-input" className="form-control" placeholder="Digite um local..." />
                <span className="input-group-btn">
                  <button className="btn btn-info" id="search" onClick={this.getAddress}> Ir </button>
                </span>
          </div>
          
          <button type="button" id="search" className="btn btn-success" onClick={this.getCurrentLocation}>
            <span className="glyphicon glyphicon-screenshot current-location" aria-hidden="true"></span>
          </button>
      
        </div>
      </div>
    );
  }
});

//Google maps component
var Gmaps = React.createClass({

  //Render search input
  render: function() {
    return (
      <div id="map"></div>
    );
  }
});

//All Components  combined to load in the index page
var App = React.createClass({

  //After Gmaps component is rendered, call this function to bind google maps
  componentDidMount: function() {
    
    //Initializing geoCoder
    geocoder = new google.maps.Geocoder();

    //Some random lanLng
    myLatlng = new google.maps.LatLng(-9.66433, -35.72968);

    //Map option
    mapOptions = {
      zoom: 15,
      center: myLatlng
    };

    //Render google maps in #map container
    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    //Adding maker to maps
    marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: 'location'
    });

    var searchBar = document.getElementById('search-input');

    //Adding autocomplete to search bar
    var autocomplete = new google.maps.places.Autocomplete(searchBar);
    autocomplete.bindTo('bounds', map); //Binding autocomplete

    //On click of autocomplete search, add marker to palce
    google.maps.event.addListener(autocomplete, 'place_changed', function(event) {
      
      marker.setVisible(false);//set marker to not visible
      
      //Selected place
      var place = autocomplete.getPlace();

      if (marker) {
        marker.setMap(null);
      }

      //Adding marker to the selected location
      var position = new google.maps.LatLng(place.geometry.location.A, place.geometry.location.F);

      //Marker
      marker = new google.maps.Marker({
        map: map,
        zoom: 25,
        position: position
      });

      map.setZoom(17);
      map.setCenter(marker.getPosition());
      
      marker.setVisible(true); //Set marker to visible
    });

  },
  
  //Render google maps and search bar in page
  render : function() {
    return (
      <div>
        <Searchbar />
        <Gmaps />
      </div> 
    )
  }
});

//Set rendering targer as #main-container
React.render(<App />, document.getElementById('main-container'));