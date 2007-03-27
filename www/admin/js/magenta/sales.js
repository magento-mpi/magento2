Mage.Sales = function(depend){
    var loaded = false;
    var Layout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    },
                    south: {
                        split:true,
                        initialSize:200,
                        minSize:100,
                        maxSize:400,
                        autoScroll:true,
                        collapsible:true,
                        collapsedTitle : '<b>Order info</b>',
                     }
                });
                
                this._layouts.add('main', Layout);
                

                Layout.beginUpdate();
                Layout.add('center', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                Layout.add('south', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                Layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Orders",closable:false}));
                Core_Layout.endUpdate();            
                loaded = true;
                
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        getLayout : function(name) {
            return this._layouts.get(name);
        },
        
        loadMainPanel : function() {
            this.init();
        }
    }
}();
