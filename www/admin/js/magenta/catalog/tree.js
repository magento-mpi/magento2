Mage.Catalog_Category_Tree = function(){
    var tree = null;
    var categoryContextMenu = null;

    return{
        loadTreeUrl: Mage.url+'mage_catalog/category/treeChildren/',
        tree: null,
        websiteId: null,

        create: function(panel){
            if (!this.tree) {
                Ext.QuickTips.init();
                var layout = Mage.Catalog.getLayout('tree');
                var baseEl = layout.getEl().createChild({tag:'div'});
                var tb = new Ext.Toolbar(baseEl.createChild({tag:'div'}));
                var treeContainer = baseEl.createChild({tag:'div'});
                var data_inputs = [
                    ['0', 'All Websites'],
                    ['1', 'Magento'],
                    ['2', 'Website 2'],
                    ['3', 'Website 3'],
                    ['4', 'Website 4']
                ];

                var websitesCombo = new Ext.form.ComboBox({
                   typeAhead: true,
                   editable: false,
                   triggerAction: 'all',
                   mode: 'local',
                   store: new Ext.data.SimpleStore({
                        fields: ['website_id', 'website_code'],
                        mode : 'local',
                        data : data_inputs
                   }),
                   displayField : 'website_code',
                   valueField : 'website_id',
                   value:'0'
                });
                websitesCombo.on('select', this.setWebsite, this);

                tb.addField(websitesCombo);
                
                categoryContextMenu = new Ext.menu.Menu({
                        id : 'category_context_menu',
                        items: [{text: 'Show Category Products', handler: this.showProducts.createDelegate(this)},
                                '-',
                                {text: 'Add child',handler:this.addChild.createDelegate(this)},
                                {text: 'Edit Catecory',handler:this.editCategory.createDelegate(this)},
                                {text: 'Delete Category',handler:this.deleteCategoryConfirm.createDelegate(this)}]
                    });

                var treePanel = layout.add('center', new Ext.ContentPanel(baseEl, {
                    fitToFrame:true,
                    autoScroll:true,
                    autoCreate:true,
                    toolbar: tb,
                    resizeEl:treeContainer
                }));
                
                var viewEl = treeContainer.createChild({tag:'div'});
                this.tree = new Ext.tree.TreePanel(viewEl, {
                    animate:true, 
                    loader: new Ext.tree.TreeLoader({dataUrl:this.loadTreeUrl}),
                    enableDD:true,
                    containerScroll: true,
                    dropConfig: {appendOnly:true}
                });
                
                this.tree.addListener('contextmenu',this.categoryRightClick,this);
                this.tree.addListener('click',this.categoryClick.createDelegate(this));
                this.tree.addListener('dblclick',this.categoryDblClick,this);
                this.tree.addListener('beforenodedrop', this.moveNode, this);

                var root = new Ext.tree.AsyncTreeNode({
                    text: 'Catalog Categories', 
                    draggable:false,
                    expanded:false
                });
                this.tree.setRootNode(root);
                this.tree.render();
            }
        },

        setWebsite: function(select, record, index){
            this.tree.loader = new Ext.tree.TreeLoader({dataUrl:this.loadTreeUrl+'website/'+index});
            this.tree.root.reload();
            this.websiteId = index;
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
                Mage.Catalog_Product.viewGrid({load: true, catId: selectedNode.id, catTitle: selectedNode.text});        
                Mage.Catalog_Product.loadCategoryEditForm(selectedNode);
                //Mage.Catalog_Category.init(selectedNode);
            } else {
                Mage.Catalog_Product.viewGrid({load: true, catId: item.parentMenu.selectedNode.id, catTitle: item.parentMenu.selectedNode.text});        
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