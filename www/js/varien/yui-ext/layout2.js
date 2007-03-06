//var Admin = function(){
//	var layout;
//    return {
//		init : function(){
//			
//			layout = new Ext.BorderLayout(document.body, {
//	                    hideOnLayout: true,
//	                    north: {
//	                        split:false,
//	                        titlebar: false,
//                		    initialSize: 22,	                        
//	                        collapsible: false
//	                    },
//	                    center: {
//	                    	resizeTabs: true,
//                    	 	alwaysShowTabs: true,
//	                    	tabPosition: 'top',
//	                        titlebar: false,
//	                        autoScroll:true,
//                            closeOnTab: true
//                        },
//		               south: {
//        		            split:false,
//                		    initialSize: 22,
//		                    titlebar: false,
//        		            collapsible: false,
//                		    animate: false
//		               }
//	                });
//	                
//                    layout.beginUpdate();
//	                layout.add('north', new Ext.ContentPanel('admin-north', {title: 'North'}));
//	                layout.add('center', new Ext.ContentPanel('admin-center',  {title: 'CenterContent', fitToFrame:true, closable: true}));
//	   				layout.add('south', new Ext.ContentPanel('admin-south'));             
//                    layout.endUpdate();
//	           },
//	           
//	           getLayout : function() {
//					return layout;
//	           }
//	     }
//}();

//Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	
//Ext.EventManager.onDocumentReady(Ext.Mage.Interface.init, Ext.Mage.Interface, true);	
