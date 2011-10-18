/**
 * Dynamic tabs that can hook-up on mouse clicks
 *
 * {license_notice}
 *
 * @category    design
 * @package     enterprise_default
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

Enterprise.Tabs = Class.create();
Object.extend(Enterprise.Tabs.prototype, {
    initialize: function (container) {
        this.container = $(container);
        this.container.addClassName('tab-list');
        this.tabs = this.container.select('dt.tab');
        this.activeTab = this.tabs.first();
        this.tabs.first().addClassName('first');
        this.tabs.last().addClassName('last');
        this.onTabClick = this.handleTabClick.bindAsEventListener(this);
        for (var i = 0, l = this.tabs.length; i < l; i ++) {
            this.tabs[i].observe('click', this.onTabClick);
        }
        this.select();
    },
    handleTabClick: function (evt) {
        this.activeTab = Event.findElement(evt, 'dt');
        this.select();
    },
    select: function () {
        for (var i = 0, l = this.tabs.length; i < l; i ++) {
            if (this.tabs[i] == this.activeTab) {
                this.tabs[i].addClassName('active');
                this.tabs[i].style.zIndex = this.tabs.length + 2;
                /*this.tabs[i].next('dd').show();*/
                new Effect.Appear (this.tabs[i].next('dd'), { duration:0.5 });
                this.tabs[i].parentNode.style.height=this.tabs[i].next('dd').getHeight() + 15 + 'px';
            } else {
                this.tabs[i].removeClassName('active');
                this.tabs[i].style.zIndex = this.tabs.length + 1 - i;
                this.tabs[i].next('dd').hide();
            }
        }
    }
});
