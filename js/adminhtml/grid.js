var varienGrid = new Class.create();

varienGrid.prototype = {
    initialize : function(containerId, url){
        this.containerId = containerId;
        this.url = url;
        this.tableSufix = '_table';

        this.trOnMouseOver  = this.rowMouseOver.bindAsEventListener(this);
        this.trOnMouseOut   = this.rowMouseOut.bindAsEventListener(this);
        this.trOnClick      = this.rowMouseClick.bindAsEventListener(this);
        this.trOnDblClick   = this.rowMouseDblClick.bindAsEventListener(this);
        this.trOnKeyPress   = this.keyPress.bindAsEventListener(this);
        
        if($(this.containerId+this.tableSufix)){
            var rows = $$('#'+this.containerId+this.tableSufix+' tbody tr');
            for (var row in rows) {
                if(row%2==0){
                    Element.addClassName(rows[row], 'even');
                }
                Event.observe(rows[row],'mouseover',this.trOnMouseOver);
                Event.observe(rows[row],'mouseout',this.trOnMouseOut);
                Event.observe(rows[row],'click',this.trOnClick);
                Event.observe(rows[row],'dblclick',this.trOnDblClick);
            } 
        }
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
        
    },
    
    rowMouseDblClick : function(event){
        
    },
    
    keyPress : function(event){
        
    },
    
    reload : function(element){
        if(element && element.name){
            var url = this.url;
            re = new RegExp('('+element.name+'\/[A-Z0-9_-]*\/)');
            url = url.replace(re, '')
            url+= element.name+'/'+element.value+'/';
            location.href = url;
        }
    }
};