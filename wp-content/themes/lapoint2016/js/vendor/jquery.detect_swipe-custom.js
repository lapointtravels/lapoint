/**
 * jquery.detectSwipe v2.1.3
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch, iPad and Android
 * http://github.com/marcandre/detect_swipe
 * Based on touchwipe by Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($) {

  $.detectSwipe = {
    version: '2.1.2',
    enabled: 'ontouchstart' in document.documentElement,
    preventDefault: true,
    threshold: 20
  };

  var startX,
    startY,
    isMoving = false;

  function onTouchEnd() {
    this.removeEventListener('touchmove', onTouchMove);
    this.removeEventListener('touchend', onTouchEnd);
    isMoving = false;
  }

  function swipeDirection( touchObject ) {
    //console.log( touchObject );
    var xDist, yDist, r, swipeAngle;

    xDist = startX - touchObject.pageX;
    yDist = startY - touchObject.pageY;
    r = Math.atan2(yDist, xDist);

    swipeAngle = Math.round(r * 180 / Math.PI);
    if (swipeAngle < 0) {
        swipeAngle = 360 - Math.abs(swipeAngle);
    }

    if ((swipeAngle <= 45) && (swipeAngle >= 0)) {
      return 'left';
        return (_.options.rtl === false ? 'left' : 'right');
    }
    if ((swipeAngle <= 360) && (swipeAngle >= 315)) {
      return 'left';
        return (_.options.rtl === false ? 'left' : 'right');
    }
    if ((swipeAngle >= 135) && (swipeAngle <= 225)) {
      return 'right';
        return (_.options.rtl === false ? 'right' : 'left');
    }

    return 'vertical';
  }

  function onTouchMove(e) {
    //console.log( e );
    var dir = swipeDirection( e.touches[0] );
    console.log( dir );

    if ($.detectSwipe.preventDefault) { e.preventDefault(); }
    if(isMoving) {
      var x = e.touches[0].pageX;
      var y = e.touches[0].pageY;
      var dx = startX - x;
      var dy = startY - y;
      var dir;
      var ratio = window.devicePixelRatio || 1;
      if(Math.abs(dx) * ratio >= $.detectSwipe.threshold) {
        dir = dx > 0 ? 'left' : 'right'
      }
      else if(Math.abs(dy) * ratio >= $.detectSwipe.threshold) {
        dir = dy > 0 ? 'up' : 'down'
      }
      if(dir) {
        onTouchEnd.call(this);
        $(this).trigger('swipe', dir).trigger('swipe' + dir);
      }
    }
  }

  function onTouchStart(e) {
    if (e.touches.length == 1) {
      startX = e.touches[0].pageX;
      startY = e.touches[0].pageY;
      isMoving = true;
      this.addEventListener('touchmove', onTouchMove, false);
      this.addEventListener('touchend', onTouchEnd, false);
    }
  }

  function setup() {
    this.addEventListener && this.addEventListener('touchstart', onTouchStart, false);
  }

  function teardown() {
    this.removeEventListener('touchstart', onTouchStart);
  }

  $.event.special.swipe = { setup: setup };

  $.each(['left', 'up', 'down', 'right'], function () {
    $.event.special['swipe' + this] = { setup: function(){
      $(this).on('swipe', $.noop);
    } };
  });
}));
