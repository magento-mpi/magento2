var varienAccordion = new Class.create();
varienAccordion.prototype = {
    initialize : function(containerId){
        this.containerId = containerId;
        this.container   = $(this.containerId);
        this.items       = $$('#'+this.containerId+' dt');
        this.loader      = new varienLoader(true);
        
        var links = $$('#'+this.containerId+' dt a');
        for(var i in links){
            if(links[i].href){
                Event.observe(links[i],'click',this.showItem.bind(this));
                this.items[i].dd = this.items[i].next('dd');
            }
        }
    },    
    showItem : function(event){
        var element = Event.findElement(event, 'dt');
        var link    = Event.findElement(event, 'a');
        
        if(element && link){
            if(link.href){
                this.loadContent(element, link);
            }
            
            this.hideAllItems();
            Element.addClassName(element, 'open');
            Element.addClassName(element.dd, 'open');
        }
        Event.stop(event);
    },
    loadContent : function(item, link){
        if(link.href.indexOf('#') == link.href.length-1){
            return;
        }
        if(link.target=='ajax'){
            this.loadingItem = item;
            this.loader.load(link.href, {}, this.setItemContent.bind(this));
            return;
        }
        location.href = link.href;
    },
    setItemContent : function(content){
        this.loadingItem.dd.innerHTML = content;
    },
    hideAllItems : function(){
        for(var i in this.items){
            if(this.items[i].id){
                Element.removeClassName(this.items[i], 'open');
                Element.removeClassName(this.items[i].dd, 'open');
            }
        }
    }
}