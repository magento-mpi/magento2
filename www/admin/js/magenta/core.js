Mage.Core = function(){
    var _layout;
    var _toolbar;
    var _toolbarItems = new Ext.util.MixedCollection();
    return {
        init: function(){
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
            _layout.add('center', new Ext.ContentPanel('center', {"title":"Center Panel","fitToFrame":true,"autoCreate":true}));
            _layout.add('south', new Ext.ContentPanel('south', {"autoCreate":true}));
            _layout.add('east',new Ext.ContentPanel('east', {"title":"My Tasks","autoCreate":true}));
            this._initToolbar();
            _layout.endUpdate();
        },
        _initToolbar : function(){
            _toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(_layout.getRegion('north').getEl().dom,{tag:'div'},true));
            _toolbar.add({id:'test', text: 'Test'});
            _toolbar.add(_toolbarItems.items);
            
            var lt = new Ext.Toolbar(Ext.DomHelper.insertFirst(_layout.getRegion('north').getEl().dom,{tag:'div'},true));
            lt.add({id:'test1', text: 'Test 1'});
            lt.getEl().setStyle('position','absolute');
            lt.getEl().setStyle('width','50%');
            lt.getEl().anchorTo(_toolbar.getEl(),'tr');
        },
        getLayoutRegion : function(region){
            return _layout.getRegion(region);
        },
        getLayout : function(){
            return _layout;
        },
        addToolbarItem : function(item){
            _toolbarItems.add(item);
        },
        updateRegion : function(region, url){
            
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