Mage.Catalog_Category_Create = function(){
    var _dialog = false;
    var _el = null
    var _config = null
    
    function _init() {
        if (!_dialog) {
            _el = Ext.DomHelper.append(document.body, {tag:'div'}, true);
            _dialog = new Ext.LayoutDialog(_el, { 
                modal: true,
                width:600,
                height:450,
                shadow:true,
                minWidth:500,
                minHeight:350,
                autoTabs:true,
                proxyDrag:true,
                // layout config merges with the dialog config
                center:{
                    tabPosition: "top",
                    alwaysShowTabs: true
                }
            });
            _dialog.addKeyListener(27, _dialog.hide, _dialog);
            _dialog.setDefaultButton(_dialog.addButton("Close", _dialog.hide, _dialog));
            
            _buildLayouts();    
        }
        if (_config.button) {
            _dialog.show(_config.button._el);
        } else {
            _dialog.show();
        }
    }
    
    function _buildLayouts() {
            // we can even add nested layouts
            var innerLayout = new Ext.BorderLayout(_el.createChild({tag:'div'}), {
                east: {
                    initialSize: 200,
                    autoScroll:true,
                    split:true
                },
                center: {
                    autoScroll:true
                }
            });
            innerLayout.beginUpdate();
            innerLayout.add("east", new Ext.ContentPanel(_el.createChild({tag:'div'})));
            innerLayout.add("center", new Ext.ContentPanel(_el.createChild({tag:'div'})));
            innerLayout.endUpdate(true);
    
            var layout = _dialog.getLayout();
            layout.beginUpdate();
            layout.add("center", new Ext.ContentPanel(_el.createChild({tag:'div'}),
                        {title: "Download the Source", fitToFrame:true}));
            layout.add("center", new Ext.NestedLayoutPanel(innerLayout,
               {title: "Build your own ext.js"}));
            layout.endUpdate();                
    }
    
    function _open() {
        
    }
    
    return {
        show : function(cfg) {
            _config = cfg;            
            _init();
        },
        
        hide : function() {
        }
    }    
}();

