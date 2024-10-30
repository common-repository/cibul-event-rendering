(new function(){

  var self = this;

  self.options = {
    scripts: ['http://code.jquery.com/jquery-1.7.2.min.js'],
    doms: {
      map: '.js_cibul_widget_map',
      eventItem: '.js_cibul_event_list_item',
      eventItemIcons: '.js_cibul_map_icons',
      location: { 
        list: '.js_cibul_location',
        lat: '.js_cibul_lat', 
        lng: '.js_cibul_lng', 
        placename: '.js_cibul_placename', 
        address: '.js_cibul_address',
        slug: '.js_cibul_slug',
      }
    },
    mapUnit: 0.005
  };

  self.init = function(){

    // load jquery and google maps

    self.loadScripts(function(){

      self.jq = jQuery.noConflict();

      // if there are no event items, remove widget altogether

      if (!self.jq(self.options.doms.eventItem).size()) {

        // just assuming that the widget div is the parent element of the map...
        self.jq(self.options.doms.map).parent().remove();

        return;

      };

      // map lat/lng pairs to page event index (will number markers according to events)
      //
      // like: locations:
      //         name: {, latitude, longitude, address, eventIndexes: [0,3 ... ]}
      //         etc and such..

      var locations = self.mapLocationsToEventItemIndexes();

      var map = self.createMap();

      // create markers, and while you are at it, add wee marker icons in marker icons div

      self.createMarkers(map, locations);     

    });

  };

  self.createMarkers = function(map, locations){

    var index = 0;
    var alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    for (slug in locations) {

      var iconSrc = 'http://www.google.com/mapfiles/marker' + alphabet.substr(index,1) + '.png';

      locations[slug].index = index;

      var marker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(locations[slug].latitude, locations[slug].longitude),
        icon: iconSrc
      });

      // adjust map bounds

      if (!index) {

        var bounds  = new google.maps.LatLngBounds(
          new google.maps.LatLng(parseFloat(locations[slug].latitude - self.options.mapUnit), parseFloat(locations[slug].longitude - self.options.mapUnit)),
          new google.maps.LatLng(parseFloat(locations[slug].latitude + self.options.mapUnit), parseFloat(locations[slug].longitude + self.options.mapUnit))
        );

      } else {

        bounds.extend(marker.getPosition());

      }

      // add icon to associated event items

      for (eventIndex in locations[slug].eventIndexes) {

        self.jq(self.options.doms.eventItem).eq(locations[slug].eventIndexes[eventIndex]).find(self.options.doms.eventItemIcons).append('<img src="' + iconSrc + '"/>');

      }

      index++;

    };

    // stick bounds to map

    map.fitBounds(bounds);

  };

  self.mapLocationsToEventItemIndexes = function(){

    // go through events in post, map locations

    var locations = {}

    self.jq(self.options.doms.eventItem).each(function(eventIndex, item){

      // looking at eventItem of index eventIndex

      self.jq(self.options.doms.location.list + ' li', item).each(function(locationIndex, locationItem){

        // pump out data from location item of event item

        var locationItem = {
          address: self.jq(self.options.doms.location.address, locationItem).html(),
          placename: self.jq(self.options.doms.location.placename, locationItem).html(),
          longitude: self.jq(self.options.doms.location.lng, locationItem).html(),
          latitude: self.jq(self.options.doms.location.lat, locationItem).html(),
          slug: self.jq(self.options.doms.location.slug, locationItem).html(),
          eventIndexes: []
        };

        // map location item if hasn't been done yet

        if (typeof locations[locationItem.slug] == 'undefined') locations[locationItem.slug] = locationItem;

        // add event index to it

        locations[locationItem.slug].eventIndexes.push(eventIndex);

      });

    });

    return locations;
  };


  self.createMap = function(callback) {

    var map = new google.maps.Map(self.jq(self.options.doms.map).eq(0).get(0), {
      center: new google.maps.LatLng(48.861779,2.352448),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      streetViewControl: false,             
      mapTypeControl: false,
      zoom: 15,
    });

    return map;

  };
  
  // load scripts listed in options
  self.loadScripts = function(callback) {

    var toBeLoaded = this.options.scripts.length;
    var loadedCount = 0;

    for (var index = 0; index<toBeLoaded; index++) {

      this.loadScript(this.options.scripts[index], function(){

        loadedCount++;

        if (loadedCount == toBeLoaded) callback();

      });

    };

  };

  // load a script, then call callback
  self.loadScript = function(src, callback) {

    var s = document.createElement('script');

    document.getElementsByTagName('head')[0].appendChild(s);

    s.onload = function() {
        //callback if existent.
        if (typeof callback == "function") callback();
        callback = null;
    }

    s.onreadystatechange = function() {
        if (s.readyState == 4 || s.readyState == "complete") {
            if (typeof callback == "function") callback();
            callback = null; // Wipe callback, to prevent multiple calls.
        }
    }

    s.charset="utf-8"

    s.src = src;
  };

}).init();