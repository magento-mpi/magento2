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

Mage.core = {};


Mage.Admin = function(){
    var _layout;
    var _leftToolbar;
    var _rightToolbar;
    var _lToolbarItems = new Ext.util.MixedCollection();
    var _rToolbarItems = new Ext.util.MixedCollection();
    return {
        layout : null,
        
        init: function(){
            Ext.get('loading').remove();            
            _layout = new Ext.BorderLayout(document.body, {
                    "hideOnLayout":true,
                    "north":{
                        "split":false,
                        "titlebar":false,
                        "collapsible":false
                    },
                    "center":{
                        resizeTabs:true,
                        alwaysShowTabs:true,
                        hideTabs:true,
                        tabPosition:'top',
                        titlebar:false,
                        autoScroll:true,
                        closeOnTab:true
                    },
                    "south":{
                        "split":false,
                        "initialSize":22,
                        "titlebar":false,
                        "collapsible":false,
                        "animate":false
                    },
                    "east":{
                        collapsedTitle : '<strong>TaskBar</strong>',
                        "split":true,
                        "initialSize":150,
                        "autoScroll":true,
                        "collapsible":true,
                        "titlebar":true,
                        "animate":false
                    }
                });
            _layout.getRegion('east').getEl().addClass('my-tasks-region');
            
            _layout.beginUpdate();
            _layout.add('north', new Ext.ContentPanel('north', {"title":"Top Panel","autoCreate":true}));
            _layout.add('center', new Ext.ContentPanel('dashboard-center', {title:"DashBoard", fitToFrame:true, autoCreate:true}, '<embed width="100%" height="100%" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" allowscriptaccess="sameDomain" name="reports" bgcolor="#869ca7" quality="high" flashvars="configUrl=/admin/mage_reports/flex/config/&cssUrl=/admin/skins/default/flex.swf" id="reports" wmode="opaque" src="/admin/flex/reports.swf"/>'));
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
        getRighToolbar : function() {
           return _rightToolbar;
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
Ext.EventManager.onDocumentReady(Mage.Admin.init, Mage.Admin, true);

Mage.Search = function() {
    return {
        init : function() {
           var ds = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: Mage.url + 'mage_core/search/do/'
                }),
                reader: new Ext.data.JsonReader({
                    root: 'items',
                    totalProperty: 'totalCount',
                    id: 'id'
                }, [
                    {name: 'type', mapping: 'type'},
                    {name: 'name', mapping: 'name'},
                    {name: 'description', mapping: 'description'}
                ])
            });

            // Custom rendering Template
            var resultTpl = new Ext.Template(
                '<div class="search-item">',
                    '<h3><span>{type}</span>{name}</h3>',
                    '{description}',
                '</div>'
            );            
            
            var comboSearch = new Ext.form.ComboBox({
                store: ds,
                displayField:'title',
                typeAhead: false,
                loadingText: 'Searching...',
                width: 250,
                pageSize:10,
                hideTrigger:true,
                tpl: resultTpl,
                onSelect: function(record){ // override default onSelect to do redirect
                    var id = record.id.split('/');
                    switch (id[0]) {
                        case 'product':
                            Mage.Catalog_Product.viewGrid({load:true, catId:id[1], catTitle:''});
                            Mage.Catalog_Product.doCreateItem(id[2], 'yes');
                            break;
                            
                        case 'customer':
                            Mage.Customer.loadMainPanel();
                            Mage.Customer.customerCardId = id[2];
                            Mage.Customer.showEditPanel();
                            break;
                            
                        case 'order':
                            Mage.Sales.loadMainPanel();
                            Mage.Sales.loadOrder({
                                id : id[2],
                                title : record.json.form_panel_title
                            })
                            break;
                    }
                }
           });
           Mage.Admin.getRighToolbar().addField(comboSearch);
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Search.init, Mage.Search, true);