Mage.Wizard = function(el, config) {
    this.dialog = null;
    this.currentPanel = null;    
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
            alwaysShowTabs: true,
            hideTabs : false
        }                
    });
    
    this.addEvents({
        cancel : true
    });
    
    this.on('beforehide', this.onBeforeHide, this);
    
    this.addButton({
        text:'Help',
        handler : this.help,
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
    
    show : function(el) {
        Mage.Wizard.superclass.show.call(this, el);
        this.next();
    },
    
    newStep : function(url) {
        var index, panel, cfg;
        cfg = {
            url : url,
            title : 'Test page'
        };
        this.currentPanel = new Mage.core.Panel(this.layout.getRegion('center'), 'view', cfg)        
        this.stepCollection.add(this.currentPanel);
        index = this.stepCollection.indexOf(this.currentPanel) || 0
        this.checkButtons(index);        
    },
    
    help : function() {
        
    },
    
    next : function() {
        var panel, index;
        index = this.stepCollection.indexOf(this.currentPanel) || 0;
        if (this.stepCollection.get(index+1)) {
            this.currentPanel = this.stepCollection.get(index+1);
            this.currentPanel.show();
        } else if (index + 1 < this.points.length) {
            this.newStep(this.points[index+1].url);  
        }
        this.checkButtons(index);        
    },

    back : function() {
        var panel, index;
        index = this.stepCollection.indexOf(this.currentPanel) || 0;
        if (this.stepCollection.get(index-1)) {
            this.currentPanel = this.stepCollection.get(index-1);
            this.currentPanel.show();
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


