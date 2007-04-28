Mage.Catalog_Category_Create = function(){
    var _dialog = false;
    var _el = null
    var _config = null
    var _formUlr = Mage.url + 'mage_catalog/category/form/';
    
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
            _dialog.setDefaultButton(_dialog.addButton("Save", saveFrom));            
            _dialog.setDefaultButton(_dialog.addButton("Close", _dialog.hide, _dialog));

            
            _buildLayouts();    
        }
        if (_config.button) {
            _dialog.show(_config.button._el);
        } else {
            _dialog.show();
        }
    }
    
    function saveFrom() {
        var panel = _dialog.getLayout().getRegion('center').getActivePanel();
        console.log(panel);
    }
    
    function _buildLayouts() {
            var layout = _dialog.getLayout();
            layout.beginUpdate();
            layout.add("center", new Ext.ContentPanel(_el.createChild({tag:'div'}),{
                title: "New Category", 
                fitToFrame:true,
                url : _formUlr
            }));
            layout.endUpdate();                
    }
    
    function _open() {
        
    }
    
    return {
        events : {
            load : true
        },
        
        show : function(cfg) {
            _config = cfg;            
            _init();
        },
        
        hide : function() {
        }
    }    
}();

//Ext.extend(Mage.Catalog_Category_Create, Ext.util.Observable, {
//    onLoad : function() {
//        fireEvent('load', this);
//    }    
//});
//
//Ext.EventManager.addListener(Mage.Catalog_Category_Create, 'load', function(){console.log(this)});