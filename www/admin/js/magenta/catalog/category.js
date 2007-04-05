Mage.Catalog_Category = function(dep){
    var loaded = false;
    var Layout = null;
    return {
        depend : null,
        treeLayout : null,
        init : function() {
            this.depend = dep;
            this.treeLayout = this.depend.getLayout('tree');
            this.buildGrid();
        },
        
        buildGrid : function() {
            
            
            var gridEl = this.treeLayout.getEl().createChild({tag:'div'});
            
            
            var propsGrid = new Ext.grid.PropertyGrid(gridEl);

    		propsGrid.setSource({
                "(name)" : 'test',
                "active" : true 
    		});
    		
            propsGrid.render()            
            this.treeLayout.add('south', new Ext.GridPanel(propsGrid));
        }
        
    }
}(Mage.Catalog);