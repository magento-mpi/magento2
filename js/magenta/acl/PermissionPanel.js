


Mage.PermissionPanel = function(){
    return {
        panel : null,
        parentLayout : null,
        actionTree : null,
        userTree : null,
        groupTree : null,
        
        
        loadMainPanel : function() {
            this.parentLayout = Mage.Admin.getLayout();
            
            if (!this.panel) {
                this.panel = this.buildPanel();
                this.parentLayout.beginUpdate();
                this.parentLayout.add('center', this.panel);
                
                this.buildUserTree(this.panel.getLayout().getRegion('west').getActivePanel().getEl());
                this.buildGroupTree(this.panel.getLayout().getRegion('center').getActivePanel().getEl());
                this.buildActionTree(this.panel.getLayout().getRegion('east').getActivePanel().getEl());                
                
                this.parentLayout.endUpdate();            
            } else {
                this.parentLayout.showPanel(this.panel);
            }
        },
        
        buildPanel : function() {
            var layout = new Ext.BorderLayout(Ext.DomHelper.append(document.body, {tag: 'div'}, true),{
                west: {
                    split:true,
                    autoScroll:true,
                    collapsible:false,
                    titlebar: true,
                    minSize : 200,
                    maxSize : 600,
                    initialSize: 200
                },
                center : {
                    autoScroll : false,
                    titlebar : true,
                    hideTabs:false,
                    minSize : 200                        
                },
                east : {
                    split:true,                        
                    autoScroll : false,
                    collapsible:false,                        
                    titlebar : true,
                    hideTabs:false,
                    minSize : 200,
                    maxSize : 600,
                    initialSize: 400
                },
                south : {
                    split:true,                        
                    autoScroll : false,
                    collapsible:false,                        
                    titlebar : true,
                    hideTabs:false,
                    minSize : 200,
                    maxSize : 600,
                    initialSize: 200
                }            
            });
           
            layout.beginUpdate();
            layout.add('west', new Ext.ContentPanel(Ext.id(), {title: 'Users', autoCreate: true}));
            layout.add('center', new Ext.ContentPanel(Ext.id(), {title: 'Groups & Roles', autoCreate: true}));
            layout.add('east', new Ext.ContentPanel(Ext.id(), {title: 'Resources & Actions', autoCreate: true}));
            layout.add('south', new Ext.ContentPanel(Ext.id(), {title: 'Properties', autoCreate: true}));
            layout.endUpdate();                
            return new Ext.NestedLayoutPanel(layout, {title:"User & Permission", closable:false});
        },
        
        buildUserTree : function(el) {
            if (this.userTree) {
                return true;
            }
            var treePanel = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'tree/user/'}),
                enableDD:true,
                containerScroll: true
            });  
            UserTree = treePanel;

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Users',
                draggable:false,
                id:'U0'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
        },
        
        buildGroupTree : function(el) {
            if (this.groupTree) {
                return true;
            }
            var treePanel = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'tree/role/'}),
                enableDD:true,
                containerScroll: true
            });  
            GroupTree = treePanel;

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Groups',
                draggable:false,
                id:'G0'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
            
        },
        
        buildActionTree : function(el) {
            if (this.actionTree) {
                return true;
            }
            
            var treePanel = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'tree/resource/'}),
                enableDD:true,
                containerScroll: true
            });  
            ActionTree = treePanel

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Actions',
                draggable:false,
                id:'_'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
        },    
    }
}();