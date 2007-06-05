Mage.Wizard = function(el, config) {
    this.dialog = null;
        
    Ext.apply(this, config);
    this.config = config || {};
    
    Mage.Wizard.superclass.constructor.call(this, el, {
        modal: true,
        width:600,
        height:450,
        shadow:true,
        minWidth:500,
        minHeight:350,
        autoTabs:true,
        proxyDrag:true,
        title : 'Wizard',
        center:{
            tabPosition: "top",
            alwaysShowTabs: false,
            hideTabs : true
        }                
    });
    
    this.addEvents({
        cancel : true
    });
    
    this.on('beforehide', this.onBeforeHide, this);
    
    this.addButton({
        text:'New Step',
        handler : this.newStep,
        scope : this
    });
    
    this.btnCancel = this.addButton({
        text : 'Cancel',
        disabled : false,
        handler : this.cancel,
        scope : this
    });
    
    this.btnBack = this.addButton({
        text : 'Back',
        disabled : false,
        handler : this.back,
        scope : this
    });
    
    this.btnNext = this.addButton({ 
        text : 'Next',
        handler : this.next,
        scope : this
    });
    
    this.btnFinish = this.addButton({ 
        text : 'Finish',
        hidden : false,
        handler : this.finish,
        scope : this
    });
    
    
    this.stepCollection = new Ext.util.MixedCollection();
}

Ext.extend(Mage.Wizard, Ext.LayoutDialog, {
    newStep : function() {
        var index, panel;
        panel = this.layout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate : true}, this.stepCollection.getCount()+1));    
        this.stepCollection.add(panel);
        index = this.stepCollection.getCount();
        this.checkButtons(index);        
    },
    
    next : function() {
        var panel, index;
        panel = this.layout.getRegion('center').getActivePanel();
        index = this.stepCollection.indexOf(panel);
        if (this.stepCollection.get(index+1)) {
            this.layout.getRegion('center').showPanel(this.stepCollection.get(index+1));
        }
        this.checkButtons(index);        
    },

    back : function() {
        var panel, index;
        panel = this.layout.getRegion('center').getActivePanel();
        index = this.stepCollection.indexOf(panel);
        if (this.stepCollection.get(index-1)) {
            this.layout.getRegion('center').showPanel(this.stepCollection.get(index-1));
        }
        this.checkButtons(index);
    },
    
    finish : function() {
        Ext.MessageBox.alert('Wizard', 'Finish pressed');
    },
    
    onBeforeHide : function(arguments) {
        this.fireEvent('cancel', arguments);
    },
    
    cancel : function() {
        this.hide();
    },
    
    checkButtons : function(index) {
    }
});

Mage.Wizard.Step = function(config) {
    Ext.apply(this, config);
}


