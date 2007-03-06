<div id='customers-tree-div'></div>
<script>
Ext.onReady(function(){
    // shorthand
    
    var tree = new Ext.tree.TreePanel('customers-tree-div', {
        animate: true, 
//        loader: new Tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_customer/tree/searches/'}),
        enableDD: false,
        containerScroll: true
    });

    // set the root node
    var root = new Ext.tree.TreeNode({
    	text: 'Customers',
    	allowDrag: false,
    	id: 'customers-root'
    });
    
    var recentCustomers = new Ext.tree.AsyncTreeNode({
        text: 'Recent Customers',
        allowDrag: false,
        id:'customers-recent',
    	loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_customer/tree/recentCustomers/'})
    });
    
    var recentSearches = new Ext.tree.AsyncTreeNode({
        text: 'Recent Searches',
        allowDrag: false,
        id:'customers-recent-searches',
    	loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_customer/tree/recentSearches/'})
    });    
   
    var savedSearches = new Ext.tree.AsyncTreeNode({
        text: 'Saved Searches',
        allowDrag: false,
        id:'customers-saved-searches',
    	loader: new Ext.tree.TreeLoader({dataUrl:'<?=$this->BASE_URL?>/mage_customer/tree/savedSearches/'})
    });
    
    tree.setRootNode(root);
    root.appendChild(recentCustomers, recentSearches, savedSearches);
    
    // render the tree
    tree.render();

    root.expand();
});
 </script>