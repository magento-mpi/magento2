Mage.Collection.add('catalog_category_tree', new Ext.tree.TreePanel('catalog-category-tree-div', {"autoCreate":true, "animate":false,"enableDD":true,"containerScroll":true}));

Mage.Collection.add('catalog_category_tree_root', new Ext.tree.AsyncTreeNode({"text":"Catalog","draggable":false,"id":"1","expanded":true,"loader":new Ext.tree.TreeLoader({"dataUrl":"/dev/moshe/magenta/www/admin/mage_catalog/category/treeChildren"})}));

Mage.Collection.get('catalog_category_tree').setRootNode(Mage.Collection.get('catalog_category_tree_root'));
Mage.Collection.get('catalog_category_tree').render();
Mage.Collection.get('catalog_category_tree_root').expand();


if (!Mage.Collection.get('category_tree_context_menu')){

    var categoryTreeHandler = new Object();

    categoryTreeHandler.showProducts = function(item, event) {
        Mage.Catalog_Product.viewGrid(item.parentMenu.selectedNode);        
    };

    categoryTreeHandler.addChild = function(item, event) {
        addChildDialog(item);
        //Mage.Catalog_Product.viewGrid(item.parentMenu.selectedNode);
        //Ext.MessageBox.alert('New Category', 'create new');        
    };


    categoryTreeHandler.editCategory = function(item,event){
        alert('edit category');
    };

    categoryTreeHandler.deleteCategory = function(flag){
        if (flag == 'yes') {
            var tree = Mage.Collection.get('catalog_category_tree');
            var node = tree.getSelectionModel().getSelectedNode();
            var success = function(o) { this.parentNode.removeChild(this);};
            var failure = function(o) { Ext.dump(o.statusText); };
            var con = new Ext.lib.Ajax.request('GET', '<?=$baseUrl?>/mage_catalog/category/delete/id/'+node.id+'/', {success:success,failure:failure,scope:node});
        }
    };

    categoryTreeHandler.deleteConfirm = function(item, event) {
        Ext.MessageBox.confirm('Tree Message', 'Are you sure ?', categoryTreeHandler.deleteCategory);
    };

    categoryTreeHandler.clickCategory = function(node,event){
        if (Mage.Collection.get('category_tree_context_menu'))
        {
            Mage.Collection.get('category_tree_context_menu').hide();
        }
    };

    categoryTreeHandler.dblClickCategory = function(node,event){

    }


    var categoryTreeContextMenu = new Ext.menu.Menu({
        id : 'category_context_menu',
        items: [{text: 'Show Category Products', handler: categoryTreeHandler.showProducts},
                '-',
                {text: 'Add child',handler:categoryTreeHandler.addChild},
                {text: 'Edit Catecory',handler:categoryTreeHandler.editCategory},
                {text: 'Delete Category',handler:categoryTreeHandler.deleteConfirm}]
    });

    categoryTreeContextMenu.showNodeContextMenu = function(node, event){
        this.selectedNode = node;
        node.select();
        this.showAt(event.getXY(),this.parentMenu,false);
    };
    Mage.Collection.add('category_tree_context_menu', categoryTreeContextMenu);
    Mage.Collection.add('category_tree_handler', categoryTreeHandler);
}

// Add Listeners
Mage.Collection.get('catalog_category_tree').addListener(
    'contextmenu',
    Mage.Collection.get('category_tree_context_menu').showNodeContextMenu,
    Mage.Collection.get('category_tree_context_menu')
);

Mage.Collection.get('catalog_category_tree').addListener(
    'click',
    Mage.Collection.get('category_tree_handler').clickCategory,
    Mage.Collection.get('category_tree_handler')
);

Mage.Collection.get('catalog_category_tree').addListener(
    'dblclick',
    Mage.Collection.get('category_tree_handler').dblClickCategory,
    Mage.Collection.get('category_tree_handler')
);

var chlildDialog;
function addChildDialog(menuItem)
{
    if (!chlildDialog) {
        chlildDialog = new Ext.BasicDialog(Ext.DomHelper.append(document.body, {tag: 'div'}, true), { 
                title : 'Add child node',
                autoTabs:true,
                width:400,
                height:300,
                modal:true,
                shadow:true,
                minWidth:300,
                minHeight:300,
                proxyDrag: true,
        });
        var sbmt = chlildDialog.addButton('Submit', chlildDialog.hide, chlildDialog);
        sbmt.disable();
        chlildDialog.addButton('Close', chlildDialog.hide, chlildDialog);
        
        var mgr = new Ext.UpdateManager(chlildDialog.body);
        mgr.on('update', function(){sbmt.enable();});
        mgr.update(Mage.url + '/mage_catalog/category/form/parent/'+menuItem.parentMenu.selectedNode.id);
    }
    chlildDialog.show(menuItem.getEl());
}

function moveNode(obj) {
//    Ext.dump(obj.target.id);
//    Ext.dump(obj.point);
//    Ext.dump(obj.dropNode.id);
//    Ext.dump('===================================');

    if ((obj.target.parentNode.id == obj.dropNode.parentNode.id) && (obj.point != 'append')) {
        Ext.MessageBox.alert("Tree Message","Can't move node here. Will be Fixed");
        obj.cancel = true;
        return obj;
    }

    var url = BASE_URL + '/mage_catalog/category/move/';

    var data = {
        id: obj.dropNode.id
    }

    switch (obj.point) {
        case 'above' :
            data.pid = obj.target.parentNode.id;
            if (obj.target.previousSibling) {
                data.aid = obj.target.previousSibling.id;
            } else {
                data.aid = 0;
            }
            break;
        case 'below' :
            data.pid = obj.target.parentNode.id;
            data.aid = obj.target.id;
        break;
        case 'append' :
            data.pid = obj.target.id;
            if (obj.target.lastChild) {
                data.aid = obj.target.lastChild.id;
            } else {
                data.aid = 0;
            }
        break;
        default :
            obj.cancel = true;
            return obj;
    }


    var success = function(o) {
        try { eval(o.responseText); } catch(e) { Ext.dump(e); }
    };
    var failure = function(o) {
        Ext.dump(o.statusText);
    };

    var pd = [];
    for(var key in data) {
        pd.push(encodeURIComponent(key), "=", encodeURIComponent(data[key]), "&");
    }
    pd.splice(pd.length-1,1);
    var con = new Ext.lib.Ajax.request('POST', url, {success:success,failure:failure, scope:obj}, pd.join(""));
}
var root = Mage.Collection.get('catalog_category_tree');
root.addListener('beforenodedrop', moveNode);