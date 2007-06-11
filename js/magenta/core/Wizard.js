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
            hideTabs : true
        }                
    });
    
    this.addEvents({
        cancel : true
    });
    
    this.on('beforehide', this.onBeforeHide, this);
    
    this.addButton({
        text:'Help',
        align : 'left',
        handler : this.help,
        scope : this
    });
    
    this.btnCancel = this.addButton({
        text : 'Cancel',
        align : 'right',
        disabled : false,
        handler : this.cancel,
        scope : this
    });
    
    this.btnBack = this.addButton({
        text : 'Back',
        align : 'center',
        disabled : false,
        handler : this.back,
        scope : this
    });
    
    this.btnNext = this.addButton({ 
        text : 'Next',
        align : 'center',
        handler : this.next,
        scope : this
    });
    
    this.btnFinish = this.addButton({ 
        text : 'Finish',
        align : 'right',        
        hidden : true,
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
    },
    
    addButton : function(config, handler, scope){
        var dh = Ext.DomHelper;
        if(!this.footer){
            this.footer = dh.append(this.bwrap, {tag: "div", cls:"x-dlg-ft"}, true);
        }
        if(!this.btnContainerLeft){
            var tb = this.footer.createChild({
                cls:"x-dlg-btns x-dlg-btns-left",
                html:'<table cellspacing="0"><tbody><tr></tr></tbody></table><div class="x-clear"></div>'
            }, null, true);
            this.btnContainerLeft = tb.firstChild.firstChild.firstChild;
        }
        if(!this.btnContainerCenter){
            var tb = this.footer.createChild({
                cls:"x-dlg-btns x-dlg-btns-center",
                html:'<table cellspacing="0"><tbody><tr></tr></tbody></table><div class="x-clear"></div>'
            }, null, true);
            this.btnContainerCenter = tb.firstChild.firstChild.firstChild;
        }
        if(!this.btnContainerRight){
            var tb = this.footer.createChild({
                cls:"x-dlg-btns x-dlg-btns-right",
                html:'<table cellspacing="0"><tbody><tr></tr></tbody></table><div class="x-clear"></div>'
            }, null, true);
            this.btnContainerRight = tb.firstChild.firstChild.firstChild;
        }
        
        
        var bconfig = {
            handler: handler,
            scope: scope,
            minWidth: this.minButtonWidth,
            hideParent:true
        };
        if(typeof config == "string"){
            bconfig.text = config;
        }else{
            if(config.tag){
                bconfig.dhconfig = config;
            }else{
                Ext.apply(bconfig, config);
            }
        }
        
        switch (bconfig.align) {
            case 'left':
                var btn = new Ext.Button(
                    this.btnContainerLeft.appendChild(document.createElement("td")),
                    bconfig
                );
            break;
            case 'center' :
                var btn = new Ext.Button(
                    this.btnContainerCenter.appendChild(document.createElement("td")),
                    bconfig
                );
            break;
            case 'right' :
                var btn = new Ext.Button(
                    this.btnContainerRight.appendChild(document.createElement("td")),
                    bconfig
                );
            break;
            default : 
                var btn = new Ext.Button(
                    this.btnContainerRight.appendChild(document.createElement("td")),
                    bconfig
                );
            break;    
        }
        this.syncBodyHeight();
        if(!this.buttons){
            this.buttons = [];
        }
        this.buttons.push(btn);
        return btn;
    },    
    
    help : function() {
        
    },
    
    next : function() {
        var index, panel, conn, form, data = null;        
        index = this.stepCollection.indexOf(this.currentPanel);
        
        if (index >= this.points.length - 1) {
            return false;
        }
                
        conn = new Ext.data.Connection();
        
        
        conn.on('requestcomplete', function(tranId, response, options){
            var result = Ext.decode(response.responseText);
            if (result.error == 0) {
                if (this.stepCollection.indexOf(this.currentPanel) + 1 < this.stepCollection.getCount()) {
                    this.currentPanel = this.stepCollection.get(this.stepCollection.indexOf(this.currentPanel) + 1);
                    this.currentPanel.update(result.tabs[0]);
                    this.currentPanel.show();                    
                } else {
                    this.currentPanel = new Mage.core.Panel(this.layout.getRegion('center'), result.tabs[0].type, result.tabs[0]);
                    this.stepCollection.add(this.currentPanel);
                }
                index = this.stepCollection.indexOf(this.currentPanel);        
                this.checkButtons(index);        
            } else {
                Ext.MessageBox.alert('Wizard panel error', result.ErrorMessage);
            }
        }, this);
        
        
        if (this.currentPanel) {
           data = this.currentPanel.save();
           console.log(data);
        }
        
        conn.request({
            url : this.points[index+1].url,
            method : 'POST',
            params : data
        })
        
    },

    back : function() {
        var panel, index;
        index = this.stepCollection.indexOf(this.currentPanel) || 0;
        if (this.stepCollection.get(index-1)) {
            this.currentPanel = this.stepCollection.get(index-1);
            this.currentPanel.show();
        }
        this.checkButtons(index-1);
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
       //console.log(this.points[index]);
       if (this.points[index]) {
           switch (this.points[index].finish) {
               case 'hidden' :
                   this.btnFinish.hide();
               break;
               case 'disable' :
                   this.btnFinish.show();
                   this.btnFinish.disable();
               break;
               case 'enable' :
                   this.btnFinish.show();
                   this.btnFinish.enable();
               break;
           }
       }
    }
    
});

Mage.Wizard.Step = function(config) {
    Ext.apply(this, config);
}


