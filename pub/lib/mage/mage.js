/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
var mage = {}; //top level mage namespace

// mage.event is a wrapper for jquery event
mage.event = (function () {
  return {
    trigger: function (customEvent, data) {
      $(document).triggerHandler(customEvent.toString(), data);
    },
    observe: function (customEvent, func) {
      $(document).on(customEvent.toString(), func);
    }
  };
}());

// load javascript by data attribute or mage.load
(function () {
  var mapping = {
    'translate': ['/pub/lib/localization/json/translate.{}.js', '/pub/lib/localization/translate.js'],
    'localize': ['/pub/lib/globalize/globalize.js', '/pub/lib/globalize/cultures/globalize.culture.{}.js',
      '/pub/lib/mage/localization/localize.js']
  };
  var syncQueue = [];
  var asyncQueue = [];

  function addToQueue(arr, queue) {
    for ( var i = 0; i < arr.length; i++ ) {
      if ( typeof arr[i] === 'string' && $.inArray(arr[i], queue) === -1) {
        queue.push(arr[i]);
      }
    }
  }

  function unique(arr) {
    var uniqueArr = [];
    for ( var i = arr.length; i--; ) {
      var val = arr[i];
      if ( $.inArray(val, uniqueArr) === -1 ) {
        uniqueArr.unshift(val);
      }
    }
    return uniqueArr;
  }

  function load_script() {
    //add sync load js file to syncQueue
    $('[data-js-sync]').each(function () {
      var jsFiles = $(this).attr('data-js-sync').split(" ");
      syncQueue = $.merge(jsFiles, syncQueue);
    });
    syncQueue = unique(syncQueue);
    if ( syncQueue.length > 0 ) {
      syncQueue.push(function () {
        async_load();
      });
      head.js.apply({}, syncQueue);
    } else {
      async_load();
    }
  }

  function async_load() {
    //add async load js file to asyncQueue
    $('[data-js]').each(function () {
      var jsFiles = $(this).attr('data-js').split(" ");
      asyncQueue = $.merge(jsFiles, asyncQueue);
    });
    asyncQueue = unique(asyncQueue);
    var x = document.getElementsByTagName('script')[0];
    for ( var i = 0; i < asyncQueue.length; i++ ) {
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = asyncQueue[i];
      x.parentNode.appendChild(s);
    }
  }

  if ( window.attachEvent )
    window.attachEvent('onload', load_script);
  else
    window.addEventListener('load', load_script, false);

  mage.load = (function () {
    return {
      jsSync: function () {
        addToQueue(arguments, syncQueue);
        return syncQueue.length;
      },
      js: function () {
        addToQueue(arguments, asyncQueue);
        return asyncQueue.length;
      }
    };
  }());

})();


