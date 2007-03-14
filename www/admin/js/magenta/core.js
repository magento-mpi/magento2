Mage = function(){
    return{
        init: function(){
        
        }
    }
}();

Mage.Collection = new Ext.util.MixedCollection;

Mage.Collection.add('module_core', true);

Mage.Core = function(){
    var layout;
    var toolbar;
    return {
        init: function(){
            layout = new Ext.BorderLayout(document.body, {
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

            layout.beginUpdate();
            layout.add('north', new Ext.ContentPanel('north', {"title":"Top Panel","autoCreate":true}));
            layout.add('center', new Ext.ContentPanel('center', {"title":"Center Panel","fitToFrame":true,"autoCreate":true}));
            layout.add('south', new Ext.ContentPanel('south', {"autoCreate":true}));
            layout.add('east',new Ext.ContentPanel('east', {"title":"My Tasks","autoCreate":true}));
            toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(layout.getRegion('north').getEl().dom,{tag:'div'},true));
            toolbar.add({
                cls: 'x-btn-right',
                text:'logout'
            });
            layout.endUpdate();
        },
        getLayuot : function(){
            return layout;
        },
        getToolbar : function(){
            return toolbar;
        },
        getLayoutRegion : function(regionName){
            return layout.getRegion(regionName);
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Core.init, Mage.Core, true);