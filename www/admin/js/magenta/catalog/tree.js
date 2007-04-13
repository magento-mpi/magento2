Mage.Catalog_Category_Tree = function(){
    var tree = null;
    var categoryContextMenu = null;

    return{
        create: function(panel){
            if (!tree) {
                var layout = Mage.Catalog.getLayout('tree');
                var treeContainer = layout.getEl().createChild({tag:'div', id:'catalog_category_tree_cont'});
                /*var tb = new Ext.Toolbar(treeContainer.createChild({tag:'div'}));
                tb.addButton({
                    text: 'Test btn'
                });*/
                
                categoryContextMenu = new Ext.menu.Menu({
                        id : 'category_context_menu',
                        items: [{text: 'Show Category Products', handler: this.showProducts.createDelegate(this)},
                                '-',
                                {text: 'Add child',handler:this.addChild.createDelegate(this)},
                                {text: 'Edit Catecory',handler:this.editCategory.createDelegate(this)},
                                {text: 'Delete Category',handler:this.deleteCategoryConfirm.createDelegate(this)}]
                    });

                var viewEl = treeContainer.createChild({tag:'div', id:'catalog_category_tree'});
                var treePanel = layout.add('center', new Ext.ContentPanel(treeContainer, {
                    title:'Catalog', 
                    fitToFrame:true,
                    autoScroll:true,
                    autoCreate:true,
                    //toolbar: tb,
                    resizeEl:viewEl
                }));
                
                tree = new Ext.tree.TreePanel(viewEl, {
                    animate:true, 
                    loader: new Ext.tree.TreeLoader({dataUrl:Mage.url+'/mage_catalog/category/treeChildren'}),
                    enableDD:true,
                    containerScroll: true,
                    dropConfig: {appendOnly:true}
                });
                
                tree.addListener('contextmenu',this.categoryRightClick,this);
                tree.addListener('click',this.categoryClick.createDelegate(this));
                tree.addListener('dblclick',this.categoryDblClick,this);
                tree.addListener('beforenodedrop', this.moveNode, this);

                var root = new Ext.tree.AsyncTreeNode({
                    text: 'Catalog Categories', 
                    draggable:false,
                    id:'1',
                    expanded:false
                });
                tree.setRootNode(root);
                tree.render();
            }
        },

        addChildDialog: function(){
        
        },

        moveNode: function(obj){
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
        },

        editNodeDialog: function(){
        
        },
        
        deleteCategoryConfirm: function(item, event){
            Ext.MessageBox.confirm('Tree Message', 'Are you sure ?', this.deleteCategory, this);
        },

        deleteCategory: function(flag){
            if (flag == 'yes') {
                var node = tree.getSelectionModel().getSelectedNode();
                var success = function(o) { this.parentNode.removeChild(this);};
                var failure = function(o) { Ext.dump(o.statusText); };
                var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_catalog/category/delete/id/'+node.id+'/', {success:success,failure:failure,scope:node});
            }
        },

        categoryRightClick: function(node, event){
            categoryContextMenu.selectedNode = node;
            node.select();
            categoryContextMenu.showAt(event.getXY(),categoryContextMenu.parentMenu,false);
        },

        categoryDblClick: function(){
            
        },
        
        categoryClick: function(node, e){
            if (categoryContextMenu){
                categoryContextMenu.hide();
            }
            this.showProducts(null, e, node);
        },
            
        //////////////// Context menu handlers /////////////
        showProducts: function(item, event, selectedNode) {
            if (selectedNode) {
                Mage.Catalog_Product.viewGrid(selectedNode.id);        
                Mage.Catalog_Product.loadCategoryEditForm(selectedNode);
                //Mage.Catalog_Category.init(selectedNode);
            } else {
                Mage.Catalog_Product.viewGrid(item.parentMenu.selectedNode.id);        
                Mage.Catalog_Product.loadCategoryEditForm(item.parentMenu.selectedNode);                
                //Mage.Catalog_Category.init(item.parentMenu.selectedNode);                
            }
        },

        addChild: function(item, event) {
            //addChildDialog(item);
        },

        editCategory: function(item,event){
            alert('edit category');
        }
    }
}();