Mage.core.Panel = function(region, type, config) {
    Mage.core.Panel.superclass.constructor.call(this);
    this.factory = Mage.core.Panel.Factory;
    return this.factory.create(type, region, config);
}

Ext.extend(Mage.core.Panel, Ext.util.Observable,{
    tbItems : new Ext.util.MixedCollection(),
    
    
    isLoaded : function() {
        return !this.notLoaded;
    },
    
    update : function() {
        console.info('update');
        return false;
    },

    getPanel : function() {
        return this.panel;
    },

    
    save : function() {
        return false;        
    },
    
    show : function() {
        this.region.showPanel(this.panel);
    },
    
    setTitle : function(title) {
        if (this.panel) {
            return this.panel.setTitle(title);
        }
    }
})

Mage.core.Panel.Factory = {
    validPanels : ['form', 'view', 'images', 'categories', 'related', 'address'],
    
    create : function(type, region, config) {
        type = type.toLowerCase();
        switch(type){
            case "form":
                return new Mage.core.PanelForm(region, config);
            case "view":
                return new Mage.core.PanelView(region, config);
            case "images":
                return new Mage.core.PanelImages(region, config);
            case "categories":
                return new Mage.core.PanelCategories(region, config);
            case "related":
                return new Mage.core.PanelRelated(region, config);
            case "address":
                return new Mage.core.PanelAddresses(region, config);
                
        }
        throw 'Panel "'+type+'" not supported.';
    }
}
