Mage.Catalog_Category_Create = function(){
    var _dialog = false;
    var _el = null
    var _config = null
    var _formUlr = Mage.url + 'mage_catalog/category/form/';
    var _formUlr = Mage.url + 'mage_catalog/category/form/';
    
    function _init() {
        var flag = false;
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
            flag = true;
        }
        if (_config.button) {
            _dialog.show(_config.button._el);
        } else {
            _dialog.show();
        }
        var panel = _dialog.getLayout().getRegion('center').getActivePanel();
        if (flag === false) {
            panel.refresh();
        }
    }
    
    function saveFrom() {
        var i = 0;
        var panel = _dialog.getLayout().getRegion('center').getActivePanel();
        var pEl = panel.getEl();
        var form = Ext.DomQuery.selectNode('form', pEl.dom);
        if (form) {
            var um = panel.getUpdateManager();
            
            function  callBack(oElement, bSuccess, oResponse) {
                console.log(arguments);
                panel.refresh();
            }
            
            um.formUpdate(form, form.action, true, callBack);
        }
    }
    
    function _buildLayouts() {
            var layout = _dialog.getLayout();
            layout.beginUpdate();
            layout.add("center", new Ext.ContentPanel(_el.createChild({tag:'div'}),{
                title: "New Category", 
                fitToFrame:true,
                loadOnce : false,
                url : _formUlr
            }));
            layout.endUpdate();                
    }
    
    function _open() {
        
    }
    
    function _close() {
        _dialog.hide();
    }
    
    return {
        show : function(cfg) {
            _config = cfg;            
            _init();
        },
        
        hide : function() {
            _close();
        }
    }    
}();