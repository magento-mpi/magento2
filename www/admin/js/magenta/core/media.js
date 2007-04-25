Mage.Core_Media = function(){
    var mediaDialog = null;
    return {
        init: function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Media',
                handler: Mage.Core_Media.showDialog.createDelegate(Mage.Core_Media)
            });
        },
        showDialog: function(){
            Ext.QuickTips.init();
            if (!mediaDialog) {
                mediaDialog = new Ext.LayoutDialog(Ext.id(), {
                    autoCreate : true,
                    width:700,
                    height:500,
                    minWidth:600,
                    minHeight:400,
                    syncHeightBeforeShow: true,
                    shadow:true,
                    fixedcenter:true,
                    center:{autoScroll:false},
                    west:{split:true,initialSize:300}
                });
                mediaDialog.setTitle('Media Browser');
                mediaDialog.setDefaultButton(mediaDialog.addButton('Cancel', mediaDialog.hide, mediaDialog));

                var layout = mediaDialog.getLayout();
                var media = layout.getEl().createChild({tag:'div', id:'media'});
                var tb = new Ext.Toolbar(media.createChild({tag:'div'}));

                var viewEl = media.createChild({tag:'div', id:'folders'});                
                
                var treePanel = layout.add('west', new Ext.ContentPanel(media, {
                    title:'Media', 
                    fitToFrame:true,
                    autoScroll:true,
                    autoCreate:true,
                    toolbar: tb,
                    resizeEl:viewEl
                }));
                
                var centerLayout = new Ext.BorderLayout(layout.getEl().createChild({tag:'div'}), {
                     center:{
                         title: 'Folder name here (1)',
                         titlebar: true,
                         autoScroll:true,
                         hideTabs : true
                     },
                     south : {
                         hideWhenEmpty : false,
                         split:true,
                         initialSize:300,
                         minSize:50,
                         titlebar: true,
                         autoScroll: true,
                         collapsible: true,
                         hideTabs : true
                     }
                 });
                 var centerPanel = layout.add('center', new Ext.NestedLayoutPanel(centerLayout, {title:'Folder name here (2)'}));
                
                var centerPanel = layout.add('center', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                
                var tree = new Ext.tree.TreePanel(viewEl, {
                    animate:true, 
                    loader: new Ext.tree.TreeLoader({dataUrl:Mage.url+'/mage_core/media/foldersTree'}),
                    enableDD:true,
                    containerScroll: true,
                    dropConfig: {appendOnly:true}
                });

                var root = new Ext.tree.AsyncTreeNode({
                    text: 'Root', 
                    draggable:false, // disable root node dragging
                    id:'/'
                });

                tb.addButton({
                    id:'add',
                    text: 'New Folder',
                    cls: 'x-btn-text-icon btn_add',
                    disabled: true
                });
                tb.addButton({
                    id:'remove',
                    text: 'Remove Folder',
                    cls: 'x-btn-text-icon btn_delete',
                    disabled: true
                });
                tb.addButton({
                    id:'reload',
                    text:'Reload',
                    handler:function(){root.reload()},
                    cls:'x-btn-text-icon btn_arrow_refresh',
                    tooltip:'Reload the tree'
                });
                btns = tb.items.map;
                
                var sm = tree.getSelectionModel();
                sm.on('selectionchange', function(){
                    var n = sm.getSelectedNode();
                    if(!n){
                        btns.add.disable();
                        btns.remove.disable();
                        return;
                     }
                     var a = n.attributes;
                     btns.add.setDisabled(false);
                     btns.remove.setDisabled(false);
                });

                tree.setRootNode(root);
                tree.render();
            }

            mediaDialog.show();
        }
    }
}();

Mage.Core_Media.init();