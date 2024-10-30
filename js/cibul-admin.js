(new function(){

  var self = this;

  self.options = {
    scripts: ['http://code.jquery.com/jquery-1.7.2.min.js'],
  };

  self.init = function(){

    // load jquery

    self.loadScripts(function(){

      self.jq = jQuery.noConflict();

      self.jq('.js_restore_default').click(function(e){

        e.preventDefault();

        self.jq(this).parents('form').find('textarea').val(self.jq(this).closest('form').find('.js_default_value').val());

      });   

    });

  };
  
  // load scripts listed in options
  self.loadScripts = function(callback) {

    var toBeLoaded = this.options.scripts.length;
    var loadedCount = 0;

    for (index in this.options.scripts) {

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