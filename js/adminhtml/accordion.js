var varienAccordion = new Class.create();
varienAccordion.prototype = {
    initialize : function(containerId){
        this.containerId = containerId;
        this.container   = $(this.containerId);
        this.items       = $$('#'+this.containerId+' dt');
        
        for(var i in this.items){
            if(this.items[i].id){
                Event.observe(this.items[i],'click',this.showItem.bind(this));
                this.items[i].dd = this.items[i].next('dd');
            }
        }
    },    
    showItem : function(event){
        var element = Event.findElement(event, 'dt');
        if(element){
            this.hideAllItems();
            Element.addClassName(element, 'open');
            Element.addClassName(element.dd, 'open');
        }
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