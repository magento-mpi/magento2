var Toolbars = function(){
	var items;
    return {
		init : function(){
				items = new Ext.util.MixedCollection;
			    function onItemClick(item){
			    	alert('You clicked the "'+item.text+'" menu item.');
			    }				
				
			    var category = new Ext.menu.Menu({
    	    		id: 'category',
        			items: [{        
    	    				text: 'New',
        					handler: onItemClick
				      	}, {
        					text: 'Edit',
		        			handler: onItemClick
			        	} , {
    	    				text: 'Remove',
			        		handler: onItemClick
        				}]
			    });

			    var product = new Ext.menu.Menu({
			        id: 'product',
			        items: [{        
				       		text: 'New',
			        		handler: onItemClick
        				}, {
		    	    		text: 'Edit',
		        			handler: onItemClick
	        			} , {
			        		text: 'Remove',
			        		handler: onItemClick
			            }]
			    });
				
				if (!document.getElementById('catalogToolBar')) {
					var holder = document.createElement('div');
					holder.id = 'catalogToolBar';
					document.body.appendChild(holder);
				}
				var catalogToolBar = new Ext.Toolbar('catalogToolBar');
				catalogToolBar.add({
					cls: 'bmenu',
					text: 'Category',
					menu: category
				}, {
					cls: 'bmenu',
					text: 'Product',
					menu: product
				});
				items.add('catalog', catalogToolBar);
			},
			
			getItems : function() {
				return items;
			},
	           
			getToolbar : function(name) {
				var res =  items.get(name);
				return res;
			}
		};
}();
	
//	Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	
	Ext.EventManager.onDocumentReady(Toolbars.init, Toolbars, true);