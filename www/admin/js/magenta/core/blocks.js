Mage.Core_Blocks = function(){
    return {
        init: function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Blocks & Layouts',
                handler: Mage.Core_Blocks.showDialog.createDelegate(Mage.Catalog_Blocks)
            });
        },
        showDialog: function(){
            Ext.QuickTips.init();
            var dlg = new Ext.LayoutDialog(Ext.id(), {
                autoCreate : true,
                width:700,
                height:500,
                minWidth:600,
                minHeight:400,
                syncHeightBeforeShow: true,
                shadow:true,
                fixedcenter:true,
                center:{autoScroll:false},
                west:{split:true,initialSize:200,minSize:150,maxSize:250}
            });
            dlg.setTitle('Blocks & Layouts');
            dlg.setDefaultButton(dlg.addButton('Cancel', dlg.hide, dlg));

            var layout = dlg.getLayout();
            var blocks = layout.getEl().createChild({tag:'div', id:'blocks'});
            var tb = new Ext.Toolbar(blocks.createChild({tag:'div'}));
            tb.addButton({
                text: 'New Block'
            });
            var viewEl = blocks.createChild({tag:'div', id:'folders'});

            var treePanel = layout.add('west', new Ext.ContentPanel(blocks, {
                title:'My Albums', 
                fitToFrame:true,
                autoScroll:true,
                autoCreate:true,
                toolbar: tb,
                resizeEl:viewEl
            }));

            var tree = new Ext.tree.TreePanel(viewEl, {
                animate:true, 
                enableDD:true,
                containerScroll: true
                //ddGroup: 'organizerDD',
                //rootVisible:false
            });

            var root = new Ext.tree.TreeNode({
                text: 'Blocks', 
                id: "1",
                loader:Mage.url+"/mage_catalog/category/treeChildren",
                expanded: true,
                allowDrag:false,
                allowDrop:false
            });
            tree.setRootNode(root);
            tree.render();
            root.expand();
            //layout.beginUpdate();
            var centerPanel = layout.add('center', new Ext.ContentPanel(Ext.id(), {
                autoCreate : true,
                fitToFrame:true
            }));
            //layout.endUpdate();

            dlg.show();
        }
    }
}();

Mage.Core_Blocks.init();