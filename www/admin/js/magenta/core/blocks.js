Mage.Core_Blocks = function(){
    var blockDialog = null;
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
            if (!blockDialog) {
                blockDialog = new Ext.LayoutDialog(Ext.id(), {
                    autoCreate : true,
                    width:700,
                    height:500,
                    minWidth:600,
                    minHeight:400,
                    syncHeightBeforeShow: true,
                    shadow:true,
                    fixedcenter:true,
                    center:{autoScroll:false},
                    west:{split:true,initialSize:200,minSize:150}
                });
                blockDialog.setTitle('Blocks & Layouts');
                blockDialog.setDefaultButton(blockDialog.addButton('Cancel', blockDialog.hide, blockDialog));

                var layout = blockDialog.getLayout();
                var blocks = layout.getEl().createChild({tag:'div', id:'blocks'});
                var tb = new Ext.Toolbar(blocks.createChild({tag:'div'}));
                tb.addButton({
                    text: 'New Block'
                });
                var viewEl = blocks.createChild({tag:'div', id:'folders'});

                var treePanel = layout.add('west', new Ext.ContentPanel(blocks, {
                    title:'Blocks', 
                    fitToFrame:true,
                    autoScroll:true,
                    autoCreate:true,
                    toolbar: tb,
                    resizeEl:viewEl
                }));
                
                var tree = new Ext.tree.TreePanel(viewEl, {
                    animate:true, 
                    loader: new Ext.tree.TreeLoader({dataUrl:Mage.url+'mage_core/block/blockChildren/'}),
                    enableDD:true,
                    containerScroll: true,
                    dropConfig: {appendOnly:true}
                });

                var root = new Ext.tree.AsyncTreeNode({
                    text: 'Config', 
                    draggable:false, // disable root node dragging
                    id:'config'
                });

                tree.setRootNode(root);
                tree.render();

                var centerPanel = layout.add('center', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
            }

            blockDialog.show();
        }
    }
}();

Mage.Core_Blocks.init();