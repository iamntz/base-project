var _Ntz = function(){
  this.init();
};

_Ntz.prototype = {
  init : function(){
    var $t = this;
    $t.enablePlaceholderSupport();
  } // init

  ,enablePlaceholderSupport : function(wrap){
    var $                  = jQuery,
        wrap               = wrap || 'body',
        fakeInput          = document.createElement("input"),
        placeHolderSupport = ("placeholder" in fakeInput),
        clearValue         = function (e) { if ($(e).val() === $(e).data('placeholder')) { $(e).val(''); } };
    if (!placeHolderSupport) {
      $('input[placeholder]', wrap).each(function(){
        var searchField  = $(this),
            originalText = searchField.attr('placeholder'),
            val          = $.trim(this.value);
        if(typeof searchField.data('placeholder') !== 'undefined') { return; }
        searchField.data('placeholder', originalText);
        if(val == '') { $(this).val(originalText).addClass("placeholder"); }
        searchField.bind("focus.ntz_placeholder", function () { if(this.value == $(this).data('placeholder')){ $(this).val('').removeClass('placeholder'); } }).bind("blur.ntz_placeholder", function () {
          if (this.value.length === 0) {
            $(this).val(originalText).addClass("placeholder");
          }
        });
      });
      $("form").bind("submit.ntz_placeholder", function () { $('input[placeholder]', this).each(function(){ clearValue(this); }); });
      $(window).bind("unload.ntz_placeholder", function () { clearValue($('input[placeholder]', this)); });
    }
  }// enablePlaceholderSupport
};

jQuery(document).ready(function($){
  var _ntz = new _Ntz();
});



// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// requestAnimationFrame polyfill by Erik Möller
// fixes from Paul Irish and Tino Zijdel

(function() {
  var lastTime = 0,
      vendors  = [ 'ms', 'moz', 'webkit', 'o' ];

  for( var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x ){
    window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
    window.cancelAnimationFrame  = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
  }

  if ( !window.requestAnimationFrame ){
    window.requestAnimationFrame = function( callback, element ){
      var currTime = new Date().getTime();
      var timeToCall = Math.max( 0, 16 - ( currTime - lastTime ) );
      var id = window.setTimeout( function(){ callback( currTime + timeToCall ); }, 
        timeToCall);
      lastTime = currTime + timeToCall;
      return id;
    };
  }

  if ( !window.cancelAnimationFrame ){
    window.cancelAnimationFrame = function( id ) {
      clearTimeout( id );
    };
  }
}());

