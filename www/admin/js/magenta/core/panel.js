Mage.core.Panel = function(region, type, config) {
    Mage.core.Panel.superclass.constructor.call(this);
    this.factory = config.factory || Mage.core.Panel.Factory;    
    return this.factory.create(type, region, config);
}

Ext.extend(Mage.core.Panel, Ext.util.Observable,{
    update : function() {
        console.info('update');
        return false;
    },
    
    save : function() {
        console.info('save');
        return false;        
    }
})

Mage.core.Panel.Factory = {
    validPanels : ['form', 'view', 'general', 'images', 'categories', 'related', 'address'],
    
    create : function(type, region, config) {
        type = type.toLowerCase();
        switch(type){
            case "form":
                return new Mage.core.PanelForm(region, config);
            case "view":
                return new Mage.core.PanelView(region, config);
            case "images":
                return new Mage.core.PanelForm(region, config);
            case "categories":
                return new Mage.core.PanelForm(region, config);
            case "related":
                return new Mage.core.PanelRelated(region, config);
            case "address":
                return new Mage.core.PanelForm(region, config);
                
        }
        throw 'Panel "'+type+'" not supported.';
    }
}