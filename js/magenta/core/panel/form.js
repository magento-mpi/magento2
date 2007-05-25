Mage.core.PanelForm = function(region, config) {
    this.region = region;
    this.config = config;
    Ext.apply(this, config);
    var background = false;
    if (config && config.background == true) {
        background = true;
    }

    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        background : background,
        autoScroll : true,
        fitToFrame : true,
        title : this.title || 'Title'
    }));

    this.panel.on('activate', function() {
        if (this.form) {
            this._buildForm();
            this.notLoaded = false;
        }
    }, this, {single : true})


    this.panel.on('activate', function() {
        if (this.notLoaded) {
            this._rebuildForm();
            this.notLoaded = false;
        }
    }, this)

};

Ext.extend(Mage.core.PanelForm, Mage.core.Panel, {
    
    update : function(config) {
        Ext.apply(this, config);    
        if (this.region.getActivePanel() == this.panel) {
            this._rebuildForm();
            this.notLoaded = false;   
        } else {
            this.notLoaded = true;   
        }

    },

    _rebuildForm : function() {
        if (!this.form) {
            return false;
        }
        var i;
        if (this.frm) {
            for (i=0; i < this.frm.items.getCount(); i++) {
                this.frm.remove(this.frm.items[i]);
            }
            delete this.frm;
            this.panel.setContent('');
            this._buildForm();
        }
    },
    
    getForm : function() {
        return this.frm;
    },
    
    _buildTemplate : function(formId) {
        if (!this.tpl) {
            this.tpl = new Ext.Template('<div>' +
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>' +
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">' +
                '<div id="{formElId}">' +
                '</div>' +
                '</div></div></div>' +
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>' +
                '</div>');
           this.tpl.compile();         
        }
        this.tpl.append(this.panel.getEl(), {formElId : formId});
    },
    
    _buildForm : function() {
        var i;
        this.frm = new Mage.form.JsonForm({
            method : this.form.config.method,
            name : this.form.config.name,
            action : this.form.config.action,
            fileUpload : this.form.config.fileupload,
            metaData : this.form.elements
        });
        this._buildTemplate(this.form.config.id + '_El');        

        this.frm.render(this.form.config.id + '_El');
    }
})
