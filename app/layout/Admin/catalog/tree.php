<div id='catalog-tree-products-div'></div>
<div id='catalog-tree-categories-div'></div>

<script type="text/javascript">

ProductNavigation = {
    init: function(){
        // shorthand
        var Tree = Ext.tree;
        
        this.tree = new Tree.TreePanel('catalog-tree-products-div', {
            animate:true, 
            enableDD:true,
            containerScroll: true
        });
        
        // set the root node
        var root = new Tree.TreeNode({
            text: 'Products',
            draggable:false,
            id:'products-navigation'
        });
        
        var recentProducts = new Ext.tree.AsyncTreeNode({
            text: 'Recent Products',
            allowDrag: false,
            id:'products-recent',
            loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_catalog/tree/recentProducts/'})
        });
        
        var recentSearches = new Ext.tree.AsyncTreeNode({
            text: 'Recent Searches',
            allowDrag: false,
            id:'products-recent-searches',
            loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_catalog/tree/recentSearches/'})
        });    
       
        var savedSearches = new Ext.tree.AsyncTreeNode({
            text: 'Saved Searches',
            allowDrag: false,
            id:'products-saved-searches',
            loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_catalog/tree/savedSearches/'})
        });

        this.tree.setRootNode(root);
        root.appendChild(recentProducts, recentSearches, savedSearches);
        
        this.tree.render();
        root.expand();

        this.tree.addListener('dblclick', this.openProductCard, this);
    },

    openProductCard: function (node, e) {
        var productId = false;
        productId=String(node.id).replace(/recent-product-/g, '');
        if (!parseInt(productId)) {
            return false;
        }

        var mainLayout = Admin.getLayout();
        var cardElementId = 'catalog_productsView'+productId;
        mainLayout.beginUpdate();
        if (!Ext.get(cardElementId)) {
            var divHolder = document.createElement('div');
            divHolder.id = cardElementId;
            document.body.appendChild(divHolder);
        }
        mainLayout.add(
                    'center', 
                    new Ext.ContentPanel(
                        cardElementId, 
                        {
                            closable: true, 
                            title: node.text, 
                            fitToFrame: true, 
                            url:BASE_URL + '/mage_catalog/product/view/product/'+productId, 
                            loadOnce: true 
                         }
                    )
        );
        mainLayout.endUpdate();
    },
    
    addRecentProduct: function(productId){
        var recentProductsNode = this.tree.getNodeById('products-recent');
        var newNodeId = 'recent-product-'+productId;
        if (!this.tree.getNodeById(newNodeId)) {
            var newNode = new Ext.tree.TreeNode({
                text: 'Product #'+productId,
                allowDrag: false,
                id:newNodeId
            });
            recentProductsNode.insertBefore(newNode)
            recentProductsNode.expand();
        }
    }
};

ProductNavigation.init();

Ext.onReady(function(){
    // shorthand
    var Tree = Ext.tree;
    
    var tree = new Tree.TreePanel('catalog-tree-categories-div', {
        animate:true, 
        loader: new Tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_catalog/tree/categories/'}),
        enableDD:true,
        containerScroll: true
    });

    var menuC = new Ext.menu.Menu({
    	id : 'mainContext',
        items: [{
        		text: 'Show Products',
        		handler: ondblClick
        	}, '-',  {
        		text: 'Edit'
        	}, {
        		text: 'Delete'
        	}]
    });

	function menuShow (node){
			//Ext.dump(node);
            menuC.show(node.ui.getEl(), 'tr-tr');
    } 


    tree.on('dblclick', ondblClick);
    
    function ondblClick(node, e) {
        var mainLayout = Admin.getLayout();
        mainLayout.beginUpdate();
        if (!Ext.get('catalog_productsGrid_category_'+node.id)) {
            var divHolder = document.createElement('div');
            divHolder.id = 'catalog_productsGrid_category_'+node.id;
            document.body.appendChild(divHolder);
        }
        mainLayout.add('center', new Ext.ContentPanel('catalog_productsGrid_category_'+node.id, {closable: true, title:'Grid - ' + node.text, fitToFrame: true, url:BASE_URL + '/mage_catalog/product/grid/category/'+node.id, loadOnce: true }));
        mainLayout.endUpdate();
    }
    
    // set the root node
    var root = new Tree.AsyncTreeNode({
        text: 'Categories',
        draggable:false,
        id:'1'
    });
    tree.setRootNode(root);

	root.on('contextmenu', menuShow, root);    
    // render the tree
    tree.render();

    //root.expand();
});
 </script>