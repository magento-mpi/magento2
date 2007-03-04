Ext.onReady(function(){
  
    var tree = new Ext.tree.TreePanel('west:center:div:tree', {
        animate:true, 
        loader: new Ext.tree.TreeLoader({dataUrl:BASE_URL+'/ecom_catalog/index/content/'}),
        enableDD:true,
        containerScroll: true
    });

	tree.on('beforenodedrop', function (e) {
		var node = e.dropNode;
		var id = node.id;
	    var new_parent_id = node.parentNode.id;
		var	ajaxData = [{
			'id':id,
			'target':e.target.id,
			'point':e.point,
			'data':e.data.id
		}];
		
		
//		var tree = e.tree;
//		var target = e.target;
//	    var data = e.data;
//    	var point = e.point;
//	    var source = e.source;
//    	var rawEvent = e.rawEvent;

//	   e.cancel = true;
		var json = Ext.encode(ajaxData);
		alert(json);
	})
	
    // set the root node
    var root = new Ext.tree.AsyncTreeNode({
        text: 'Categories Tree',
        draggable:false,
        id:'1'
    });
    tree.setRootNode(root);

    // render the tree
    tree.render();
});