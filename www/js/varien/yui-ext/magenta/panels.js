Ext.Mage.Panels = {
    
        loadPanel : function() {
            
            this.layout.beginUpdate(); 
            
            var innerLayout = new Ext.BorderLayout(Ext.DomHelper.append(this.layout.getEl(), {tag:'div'}, true), {
                west: {
                    split:true,
                    autoScroll:true,
                    collapsible:true,
                    titlebar: true
	            },
    	        center: {
                    autoScroll:true
	            }
            });
            
            var westInnerLayout = new Ext.BorderLayout(Ext.DomHelper.append(this.layout.getEl(), {tag:'div'}, true), {
                center : {
                    autoScroll:true,
                    titlebar: false
                },
                south : {
                    split:true,                    
                    initialSize: 200,
                    minSize: 50,
                    maxSize: 400,
                    autoScroll:true,
                    collapsible:true,
                }
            });
            
            westInnerLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            westInnerLayout.add('south', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            
            innerLayout.add('west', new Ext.NestedLayoutPanel(westInnerLayout));
            innerLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            
            this.layout.add('center', new Ext.NestedLayoutPanel(innerLayout, {title: 'Blocks and Layouts'}));
            this.layout.endUpdate();
        
        },
    
        init : function(config) {
            this.layout = null;
            Ext.apply(this, config);
            this.region = this.layout.getRegion('center');        
            this.loadPanel();    
        }
};