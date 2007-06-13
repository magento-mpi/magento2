


Mage.PermissionPanel = function(){
    return {
        panel : null,
        parentLayout : null,
        actionTree : null,
        actionTreePanel : null,
        userTree : null,
        userTreePanel : null,        
        groupTree : null,
        groupTreePanel : null,        
        
        loadMainPanel : function() {
            this.parentLayout = Mage.Admin.getLayout();
            
            if (!this.panel) {
                this.panel = this.buildPanel();
                this.parentLayout.beginUpdate();
                this.parentLayout.add('center', this.panel);
                
                this.buildUserTree(this.userTreePanel.getEl());
                this.buildGroupTree(this.groupTreePanel.getEl());
                this.buildActionTree(this.actionTreePanel.getEl());
                
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
                }            
            });
            
            var westLayout = new Ext.BorderLayout(Ext.DomHelper.append(document.body, {tag: 'div'}, true),{
                center : {
                    autoScroll : false,
                    titlebar : false,
                    hideTabs:false,
                    minSize : 200                        
                }            
            });

            var centerLayout = new Ext.BorderLayout(Ext.DomHelper.append(document.body, {tag: 'div'}, true),{
                center : {
                    autoScroll : false,
                    titlebar : false,
                    hideTabs:false,
                    minSize : 200                        
                }            
            });

            var eastLayout = new Ext.BorderLayout(Ext.DomHelper.append(document.body, {tag: 'div'}, true),{
                center : {
                    autoScroll : false,
                    titlebar : false,
                    hideTabs:false,
                    minSize : 200                        
                }            
            });
            
            westLayout.beginUpdate();
            var userTreePanelEl = Ext.DomHelper.append(document.body, {tag:'div'}, true);
            var userTreePanelToolbar = new Ext.Toolbar(Ext.DomHelper.append(userTreePanelEl, {tag : 'div'}, true));
            userTreePanelToolbar.add({
                text : 'Reload',
                handler : function() {
                    this.userTree.root.reload();
                },
                scope : this
            });
            
            this.userTreePanel = westLayout.add('center', new Ext.ContentPanel(userTreePanelEl, {
                toolbar : userTreePanelToolbar
            }));
            westLayout.endUpdate();
            
            centerLayout.beginUpdate();
            var groupTreePanellEl = Ext.DomHelper.append(document.body, {tag:'div'}, true);
            var groupTreePanelToolbar = new Ext.Toolbar(Ext.DomHelper.append(groupTreePanellEl, {tag : 'div'}, true));
            groupTreePanelToolbar.add({
                text : 'Reload',
                handler : function() {
                    this.groupTree.root.reload();
                },
                scope : this
            });
            
            this.groupTreePanel = centerLayout.add('center', new Ext.ContentPanel(groupTreePanellEl, {
                toolbar : groupTreePanelToolbar
            }));
            centerLayout.endUpdate();
            
            eastLayout.beginUpdate();
            var actionTreePanelEl = Ext.DomHelper.append(document.body, {tag:'div'}, true);
            var actionTreePanelToolbar = new Ext.Toolbar(Ext.DomHelper.append(actionTreePanelEl, {tag : 'div'}, true));
            actionTreePanelToolbar.add({
                text : 'Reload',
                handler : function() {
                    this.actionTree.root.reload();
                },
                scope : this
            });
            
            this.actionTreePanel = eastLayout.add('center', new Ext.ContentPanel(actionTreePanelEl, {
                toolbar : actionTreePanelToolbar
            }));
            eastLayout.endUpdate();
           
            layout.beginUpdate();
            layout.add('west', new Ext.NestedLayoutPanel(westLayout, {title: 'Users'}));
            layout.add('center', new Ext.NestedLayoutPanel(centerLayout, {title: 'Groups & Roles', autoCreate: true}));
            layout.add('east', new Ext.NestedLayoutPanel(eastLayout, {title: 'Resources & Actions', autoCreate: true}));
            layout.endUpdate();                
            return new Ext.NestedLayoutPanel(layout, {title:"User & Permission", closable:false});
        },
        
        buildUserTree : function(el) {
            if (this.userTree) {
                return true;
            }
           this.userTree = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'acl/userTree/'}),
                enableDD:true,
                containerScroll: true
            });  

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Users',
                draggable:false,
                id:'U0'
            });
            this.userTree.setRootNode(root);

            // render the tree
            this.userTree.render();
            root.expand();            
        },
        
        buildGroupTree : function(el) {
            if (this.groupTree) {
                return true;
            }
            this.groupTree = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'acl/roleTree/'}),
                enableDD:true,
                containerScroll: true
            });  

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Groups',
                draggable:false,
                id:'G0'
            });
            this.groupTree.setRootNode(root);

            // render the tree
            this.groupTree.render();
            root.expand();            
            
        },
        
        buildActionTree : function(el) {
            if (this.actionTree) {
                return true;
            }
            
            this.actionTree = new Ext.tree.TreePanel(el.createChild({tag:'div'}), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + 'acl/resourceTree/'}),
                enableDD:true,
                containerScroll: true
            });  

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'All Actions',
                draggable:false,
                id:'_'
            });
            this.actionTree.setRootNode(root);

            // render the tree
            this.actionTree.render();
            root.expand();            
        },    
    }
}();