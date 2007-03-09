Ext.UpdateManager.defaults.loadScripts  = true;
Ext.UpdateManager.defaults.disableCaching  = true;

Mage = new Object();

Mage.Collection = new Ext.util.MixedCollection;

Mage.MenuHandler = {

    loadScriptSuccess : function (response) {
        try {
            eval(response.responseText);
        } catch (e) {
            alert(e);
        }
    },
    
    loadScriptFailure : function () {
        alert('ftest');
        Ext.dump(this);
    },
    
    loadScript : function (node, e) {
        var data = this;
        
        var cb = {
            success : Mage.MenuHandler.loadScriptSuccess,
            failure : Mage.MenuHandler.loadScriptFailure,
            argument : this
            
        }
        var con = new Ext.lib.Ajax.request('GET', BASE_URL + '/mage_catalog/index/addPanel/', cb);  
    },

    loadPanel : function(node, e) {

        var la = Mage.Collection.get('layout');

		// PanelId for new panel (node.id - id of clicked button)
		var	panelName = node.id + '_panel';

		// get center region - container for panel
		var center = la.getRegion('center');

		// get activePanel if exists make active
		var activePanel = center.getPanel(panelName);

		if (activePanel) {
			la.showPanel(activePanel);
		}  else { // if panel not exists make check div container for this
			if (!document.getElementById(panelName)) {
				// if container not found - make new
				divHolder = Ext.DomHelper.append(document.body, {id:panelName, tag: 'div'});
			}

			// start update layout
			la.beginUpdate();
			// create new panel with parameters, it is ajax panel - load contet from server
			la.add('center', new Ext.ContentPanel(panelName, {
                title : this.title,
                autoCreate: true,
                url: this.url,
                loadOnce : true,
                closable : false
			}));
			// after creation this panel is active
			la.endUpdate();
		}
    }
}