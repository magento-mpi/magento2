// old school cookie functions grabbed off the web
var Cookies = {};
Cookies.set = function(name, value){
     var argv = arguments;
     var argc = arguments.length;
     var expires = (argc > 2) ? argv[2] : null;
     var path = (argc > 3) ? argv[3] : '/';
     var domain = (argc > 4) ? argv[4] : null;
     var secure = (argc > 5) ? argv[5] : false;
     document.cookie = name + "=" + escape (value) +
       ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
       ((path == null) ? "" : ("; path=" + path)) +
       ((domain == null) ? "" : ("; domain=" + domain)) +
       ((secure == true) ? "; secure" : "");
};

Cookies.get = function(name){
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	var j = 0;
	while(i < clen){
		j = i + alen;
		if (document.cookie.substring(i, j) == arg)
			return Cookies.getCookieVal(j);
		i = document.cookie.indexOf(" ", i) + 1;
		if(i == 0)
			break;
	}
	return null;
};

Cookies.clear = function(name) {
  if(Cookies.get(name)){
    document.cookie = name + "=" +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
};

Cookies.getCookieVal = function(offset){
   var endstr = document.cookie.indexOf(";", offset);
   if(endstr == -1){
       endstr = document.cookie.length;
   }
   return unescape(document.cookie.substring(offset, endstr));
};


Mage.Core = function(){
    var _layout;
    var _leftToolbar;
    var _rightToolbar;
    var _lToolbarItems = new Ext.util.MixedCollection();
    var _rToolbarItems = new Ext.util.MixedCollection();
    return {
        init: function(){
            //Ext.get('loading').remove();            
            _layout = new Ext.BorderLayout(document.body, {
                    "hideOnLayout":true,
                    "north":{
                        "split":false,
                        "titlebar":false,
                        "collapsible":false
                    },
                    "center":{
                        "resizeTabs":false,
                        "alwaysShowTabs":false,
                        "hideTabs":false,
                        "tabPosition":"top",
                        "titlebar":false,
                        "autoScroll":true,
                        "closeOnTab":true
                    },
                    "south":{
                        "split":false,
                        "initialSize":22,
                        "titlebar":false,
                        "collapsible":false,
                        "animate":false
                    },
                    "east":{
                        collapsedTitle : '<b>TaskBar</b>',
                        "split":true,
                        "initialSize":150,
                        "autoScroll":true,
                        "collapsible":true,
                        "titlebar":true,
                        "animate":false
                    }
                });

            _layout.beginUpdate();
            _layout.add('north', new Ext.ContentPanel('north', {"title":"Top Panel","autoCreate":true}));
            _layout.add('center', new Ext.ContentPanel('center', {"title":"Center Panel","fitToFrame":true,"autoCreate":true, loadOnce: true, "url":Mage.url+"/mage_catalog/category/new"}));
            _layout.add('south', new Ext.ContentPanel('south', {"autoCreate":true}));
            _layout.add('east',new Ext.ContentPanel('east', {"title":"My Tasks","autoCreate":true}));
            this._initToolbar();
            _layout.endUpdate();

            // For testing
            //Mage.Catalog_Category.loadAttributesPanel();
        },
        _initToolbar : function(){
            _leftToolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(_layout.getRegion('north').getEl().dom,{tag:'div'},true));
            _leftToolbar.add(_lToolbarItems.items);
            _leftToolbar.getEl().addClass('left-menu-toolbar')
            
            _rightToolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(_layout.getRegion('north').getEl().dom,{tag:'div'},true));
            _rightToolbar.add(_rToolbarItems.items);
            _rightToolbar.getEl().addClass('right-menu-toolbar')
        },
        getLayoutRegion : function(region){
            return _layout.getRegion(region);
        },
        getLayout : function(){
            return _layout;
        },
        addLeftToolbarItem : function(item){
            _lToolbarItems.add(item);
        },
        addRightToolbarItem : function(item){
            _rToolbarItems.add(item);
        },
        updateRegion : function(region, url){
            
        },
        applyDbUpdates : function(){
            var success = function(o) {Ext.MessageBox.alert('Apply DB Updates',o.responseText);}
            var failure = function(o) {Ext.MessageBox.alert('Apply DB Updates',o.statusText);}
            var cb = {
                success : success,
                failure : failure,
                argument : {}
            };
            var con = new Ext.lib.Ajax.request('GET', Mage.url + '/index/applyDbUpdates', cb);
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Core.init, Mage.Core, true);

Mage.Loader = function(){
    return{
        request : function (url) {
            var success = function(o) { try { eval(o.responseText); } catch(e) { Ext.dump(e); } }
            var failure = function(o) { Ext.dump(o.statusText); }
            var con = new Ext.lib.Ajax.request('GET', url, {success:success,failure:failure});  
        }
    }    
}();