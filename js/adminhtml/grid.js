var varienGrid = new Class.create();

varienGrid.prototype = {
    initialize : function(containerId, url, pageVar, sortVar, dirVar, filterVar){
        this.containerId = containerId;
        this.url = url;
        this.pageVar = pageVar || false;
        this.sortVar = sortVar || false;
        this.dirVar  = dirVar || false;
        this.filterVar  = filterVar || false;
        this.tableSufix = '_table';
        this.useAjax = false;
        this.rowClickCallback = false;
        this.checkboxCheckCallback = false;
        this.reloadParams = false;

        this.trOnMouseOver  = this.rowMouseOver.bindAsEventListener(this);
        this.trOnMouseOut   = this.rowMouseOut.bindAsEventListener(this);
        this.trOnClick      = this.rowMouseClick.bindAsEventListener(this);
        this.trOnDblClick   = this.rowMouseDblClick.bindAsEventListener(this);
        this.trOnKeyPress   = this.keyPress.bindAsEventListener(this);

        this.thLinkOnClick      = this.doSort.bindAsEventListener(this);
        this.initGrid();
    },
    initGrid : function(){
        if($(this.containerId+this.tableSufix)){
            var rows = $$('#'+this.containerId+this.tableSufix+' tbody tr');
            for (var row in rows) {
                if(row%2==0){
                    Element.addClassName(rows[row], 'even');
                }
                if(rows[row].tagName) {
                    Element.addClassName(rows[row], 'pointer');
                }
                
                Event.observe(rows[row],'mouseover',this.trOnMouseOver);
                Event.observe(rows[row],'mouseout',this.trOnMouseOut);
                Event.observe(rows[row],'click',this.trOnClick);
                Event.observe(rows[row],'dblclick',this.trOnDblClick);
            }
        }
        if(this.sortVar && this.dirVar){
            var columns = $$('#'+this.containerId+this.tableSufix+' thead a');

            for(var col in columns){
                Event.observe(columns[col],'click',this.thLinkOnClick);
            }
        }
        this.bindFilterFields();
    },
    getContainerId : function(){
        return this.containerId;
    },
    rowMouseOver : function(event){
        var element = Event.findElement(event, 'tr');
        Element.addClassName(element, 'on-mouse');
    },
    rowMouseOut : function(event){
        var element = Event.findElement(event, 'tr');
        Element.removeClassName(element, 'on-mouse');
    },
    rowMouseClick : function(event){
        if(this.rowClickCallback){
            try{
                this.rowClickCallback(this, event);
            }
            catch(e){}
        }
        varienGlobalEvents.fireEvent('gridRowClick', event);
    },
    rowMouseDblClick : function(event){
        varienGlobalEvents.fireEvent('gridRowDblClick', event);
    },
    keyPress : function(event){

    },
    doSort : function(event){
        var element = Event.findElement(event, 'a');

        if(element.name && element.target){
            this.addVarToUrl(this.sortVar, element.name);
            this.addVarToUrl(this.dirVar, element.target);
            this.reload(this.url);
        }
        Event.stop(event);
        return false;
    },
    loadByElement : function(element){
        if(element && element.name){
            this.reload(this.addVarToUrl(element.name, element.value));
        }
    },
    reload : function(url){
        url = url || this.url;
        if(this.useAjax){
            new Ajax.Updater(
                this.containerId,
                url+'?ajax=true',
                {
                    onComplete:this.initGrid.bind(this), 
                    evalScripts:true,
                    parameters:this.reloadParams || {}
                }
            );
            return;
        }
        else{
            if(this.reloadParams){
                url+= (this.reloadParams.indexOf('?') > -1 ? '&' : '?') + Hash.toQueryString(this.reloadParams);
            }
            location.href = url;
        }
    },
    addVarToUrl : function(varName, varValue){
        var re = new RegExp('\/('+varName+'\/.*?\/)');
        this.url = this.url.replace(re, '/');
        this.url+= varName+'/'+varValue+'/';
        //this.url = this.url.replace(/([^:])\/{2,}/g, '$1/');
        return this.url;
    },
    doExport : function(){
        if($(this.containerId+'_export')){
            location.href = $(this.containerId+'_export').value;
        }
    },
    bindFilterFields : function(){
        var filters = $$('#'+this.containerId+' .filter input', '#'+this.containerId+' .filter select');
        for (var i in filters){
            Event.observe(filters[i],'keypress',this.filterKeyPress.bind(this));
        }
    },
    filterKeyPress : function(event){
        if(event.keyCode==Event.KEY_RETURN){
            this.doFilter();
        }
    },
    doFilter : function(){
        var filters = $$('#'+this.containerId+' .filter input', '#'+this.containerId+' .filter select');
        var elements = [];
        for(var i in filters){
            if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        this.reload(this.addVarToUrl(this.filterVar, encode_base64(Form.serializeElements(elements))));
    },
    resetFilter : function(){
        this.reload(this.addVarToUrl(this.filterVar, ''));
    },
    checkCheckboxes : function(element){
        elements = Element.getElementsBySelector($(this.containerId), 'input[name="'+element.name+'"]');
        for(var i=0; i<elements.length;i++){
            this.setCheckboxChecked(elements[i], element.checked);
        }
    },
    setCheckboxChecked : function(element, checked){
        element.checked = checked;
        if(this.checkboxCheckCallback){
            this.checkboxCheckCallback(this,element,checked);
        }
    },
    inputPage : function(event, maxNum){
        var element = Event.element(event);
        var keyCode = event.keyCode || event.which;
        if(keyCode==Event.KEY_RETURN){
            this.setPage(element.value);
        }
        /*if(keyCode>47 && keyCode<58){
            
        }
        else{
             Event.stop(event);
        }*/
    },
    setPage : function(pageNumber){
        this.reload(this.addVarToUrl(this.pageVar, pageNumber));
    }
};

function openGridRow(grid, event){
    var element = Event.findElement(event, 'tr');
    var link = Event.findElement(event, 'a');
    if(link.href){
        return;
    }
    if(element.id){
        setLocation(element.id);
    }
}