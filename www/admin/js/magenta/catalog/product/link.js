Mage.Catalog_Product_Link = function(){
    return{
        createRelatedPanel: function(panel, tabInfo){
            var baseEl = panel.getEl().createChild({tag:'div'});
            //var tb = new Ext.Toolbar(baseEl.createChild({tag:'div'}));
            var tb = new Ext.Toolbar(baseEl);
            tb.addButton ({
                    text: 'Add Products',
                    id : 'add_rel_product',
                    disabled : false,
                    //handler : this.onAddCategory.createDelegate(this),
                    cls: 'x-btn-text-icon btn-add'
                });

            var ttt = new Ext.ContentPanel('productCard_' + tabInfo.name,{
                            title : tabInfo.title || 'Related products',
                            toolbar: tb,
                            autoCreate: true,
                            closable : false,
                            loadOnce: true,
                            background: true
                        });
            return ttt;
        },
        
        createBundlePanel: function(panel){
        
        },
        
        createSuperPanel: function(panel){
        
        }
    }
}();