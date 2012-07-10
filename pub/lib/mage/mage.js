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

mage.localize = (function () {
  return {
    translate: function (val) {
      return val;
    }
  };
}());

// load javascript by data attribute or mage.load
(function () {
  var syncQueue = [];
  var asyncQueue = [];
  var cssQueue = [];

  function addToQueue(arr, queue) {
    for ( var i = 0; i < arr.length; i++ ) {
      if ( typeof arr[i] === 'string' && $.inArray(arr[i], queue) === -1 ) {
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
    var x, s, i;
    //add async load js file to asyncQueue
    $('[data-js]').each(function () {
      var jsFiles = $(this).attr('data-js').split(" ");
      asyncQueue = $.merge(jsFiles, asyncQueue);
    });
    asyncQueue = unique(asyncQueue);
    x = document.getElementsByTagName('script')[0];
    for ( i = 0; i < asyncQueue.length; i++ ) {
      s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = asyncQueue[i];
      x.parentNode.appendChild(s);
    }
    for ( i = 0; i < cssQueue.length; i++ ) {
      s = document.createElement('link');
      s.type = 'text/css';
      s.rel = 'stylesheet';
      s.href = asyncQueue[i];
      x.parentNode.appendChild(s);
    }

  }

  $(window).on('load', load_script);
  mage.load = (function () {
    return {
      jsSync: function () {
        addToQueue(arguments, syncQueue);
        return syncQueue.length;
      },
      js: function () {
        addToQueue(arguments, asyncQueue);
        return asyncQueue.length;
      },
      css: function () {
        addToQueue(arguments, cssQueue);
        return cssQueue.length;
      },
      language: function (lang) {
        var defaultLangauge = 'en';
        var cookieName = 'language';
        var language = (lang != null) ? lang : $.cookie(cookieName);
        if ( (language != null ) && (language !== defaultLangauge  ) ) {
          var mapping = {
            'localize': ['/pub/lib/globalize/globalize.js', '/pub/lib/globalize/cultures/globalize.culture.' + language + '.js', '/pub/lib/localization/json/translate_' + language + '.js',
              '/pub/lib/mage/localization/localize.js']
          };
          addToQueue(mapping.localize, syncQueue);
        }
        return syncQueue.length;
      }

    };
  }());
})();


