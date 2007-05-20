Mage.Tasks = function() {
    Mage.Tasks.superclass.constructor.call(this);
};

Ext.extend(Mage.Tasks, Ext.util.Observable, {
    init : function(admin) {
        
        var panel1 = new Ext.InfoPanel(admin.taskPanel.getEl().createChild({tag : 'div'}), "Info panel - with default config");
        var calendarEl = panel1.getBodyEl().createChild({tag : 'div'});
        calendar = new Ext.DatePicker({});
        calendar.render(calendarEl);


        var panel2 = new Ext.InfoPanel(admin.taskPanel.getEl().createChild({tag : 'div'}), {
            collapsed: true, 
            title: 'Info panel - initially collapsed',
            content: 'Nam venenatis nonummy quam....'
        });

        var panel3 = new Ext.InfoPanel(admin.taskPanel.getEl().createChild({tag : 'div'}), {
            animate: false,
            title: 'Info panel - animation disabled',
            content: 'Ut placerat. Aenean quis erat...'
        });

        var panel4 = new Ext.InfoPanel(admin.taskPanel.getEl().createChild({tag : 'div'}), {
            collapsed: true,
            trigger: 'title',
            title: 'Info panel - title click expands',
            content: 'Donec lorem erat, ultricies eget...'
        });
    }    
});

Mage.mod_Tasks = new Mage.Tasks();