Mage.Auth = function(depend){
    var loaded = false;
    var Layout = null;
    var UserTree = null;
    var GroupTree = null;
    var ActionTree = null;        
    return {
        _layouts : new Ext.util.MixedCollection(true),
        
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
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
                        minSize : 200,                        
                    },
                    east : {
                        split:true,                        
                        autoScroll : false,
                        collapsible:false,                        
                        titlebar : true,
                        hideTabs:false,
                        minSize : 200,
                        maxSize : 600,
                        initialSize: 200
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
                
                this._layouts.add('main', Layout);
                Layout.beginUpdate();
                Layout.add('west', new Ext.ContentPanel(Ext.id(), {title: 'west', autoCreate: true}));
                Layout.add('center', new Ext.ContentPanel(Ext.id(), {title: 'center', autoCreate: true}));
                Layout.add('east', new Ext.ContentPanel(Ext.id(), {title: 'east', autoCreate: true}));
                Layout.add('south', new Ext.ContentPanel(Ext.id(), {title: 'south', autoCreate: true}));
                Layout.endUpdate();                
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"User & Permission",closable:false}));
                Core_Layout.endUpdate();            
                loaded = true;
                
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        getLayout : function(name) {
            return this._layouts.get(name);
        },
        
        createUserTree : function(region) {
            if (UserTree) {
                return true;
            }
            var treePanel = new Ext.tree.TreePanel(Ext.DomHelper.append(Layout.getRegion(region).getActivePanel().getEl().dom, {tag:'div'}, true), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + '/mage_auth/tree/user/'}),
                enableDD:true,
                containerScroll: true
            });  
            UserTree = treePanel;

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'Users',
                draggable:false,
                id:'_'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
        },
        
        createGroupTree : function(region) {
            if (GroupTree) {
                return true;
            }
            var treePanel = new Ext.tree.TreePanel(Ext.DomHelper.append(Layout.getRegion(region).getActivePanel().getEl().dom, {tag:'div'}, true), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + '/mage_auth/tree/role/'}),
                enableDD:true,
                containerScroll: true
            });  
            GroupTree = treePanel;

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'Groups',
                draggable:false,
                id:'_'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
            
        },
        
        createActionTree : function(region) {
            if (ActionTree) {
                return true;
            }
            
            var treePanel = new Ext.tree.TreePanel(Ext.DomHelper.append(Layout.getRegion(region).getActivePanel().getEl().dom, {tag:'div'}, true), {
                animate:true, 
                loader: new Ext.tree.TreeLoader({dataUrl:Mage.url + '/mage_auth/tree/resource/'}),
                enableDD:true,
                containerScroll: true
            });  
            ActionTree = treePanel

            // set the root node
            var root = new Ext.tree.AsyncTreeNode({
                text: 'Actions',
                draggable:false,
                id:'_'
            });
            treePanel.setRootNode(root);

            // render the tree
            treePanel.render();
            root.expand();            
        },
        
        loadMainPanel : function() {
            this.init();
            this.createUserTree('west');
            this.createGroupTree('center');
            this.createActionTree('east');
        }
    }
}();