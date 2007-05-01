Mage.Sales = function(depend){
    return {
        layout : null,
        centerLayout : null,
        
        webSiteTree : null,
        
        oTree : null,
        websiteCBUrl : Mage.url + 'mage_core/website/list/',
        websitesTreeUrl : Mage.url + 'mage_sales/order/tree/',
        
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!this.layout) {
                this.layout =  new Ext.BorderLayout(Core_Layout.getEl().createChild({tag:'div'}), {
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    },
                    west: {
                        split:true,
                        initialSize:200,
                        minSize:100,
                        maxSize:400,
                        autoScroll:false,
                        collapsible:true,
                        hideTabs:true
                     }
                });
                
                this.centerLayout = new Ext.BorderLayout(Core_Layout.getEl().createChild({tag:'div'}), {
                     center:{
                         titlebar: true,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : false,
                         tabPosition: 'top'
                     },
                     south : {
                         hideWhenEmpty : true,
                         split:true,
                         initialSize:300,
                         minSize:50,
                         titlebar: true,
                         autoScroll: true,
                         collapsible: true,
                         hideTabs : true
                     }
                 });
                 
                this.centerLayout.beginUpdate();
                this.centerLayout.add('center', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.centerLayout.add('south', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.centerLayout.endUpdate();

                this.layout.beginUpdate();
                this.layout.add('center', new Ext.NestedLayoutPanel(this.centerLayout, {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.layout.add('west', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(this.layout, {title:"Orders", closable:false}));
                Core_Layout.endUpdate();            
                
                this.loadWebSitesTree();
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(this.layout);
            }
        },
        
        loadMainPanel : function() {
            this.init();

        },
        
        loadWebSitesTree : function() {
            this.initWebSiteTree();            
        },
        
        initWebSiteTree : function() {
            var layoutEl = this.layout.getEl();
            if (!layoutEl) {
                return false;
            }
            
            panelEl = layoutEl.createChild({children:[{id:'tree-tb'},{id:'tree-body'}]});
            var tb = new Ext.Toolbar('tree-tb');
            
            var panel = this.layout.add('west', new Ext.ContentPanel(panelEl, {
                fitToFrame : true,
                autoScroll:true,
                resizeEl : panelEl,
                toolbar : tb
            }))
            
            this.oTree = new Ext.tree.TreePanel(panel.getEl().createChild({id:Ext.id()}),{
                animate:true,
                enableDD:true,
                containerScroll: true,
                lines:false,
                rootVisible:false,
                loader: new Ext.tree.TreeLoader()
            });
            
            var wsRoot = new Ext.tree.AsyncTreeNode({
                allowDrag:true,
                allowDrop:true,
                id:'wsroot',
                text:'WebSites',
                cls:'wsroot',
                loader:new Ext.tree.TreeLoader({
                    dataUrl: this.websitesTreeUrl
                })
            });
                
            this.oTree.setRootNode(wsRoot);
            this.oTree.render();
            wsRoot.expand();            
        }
    }
}();
