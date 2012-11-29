/**
 *
 * @license     {}
 */

(function($) {
    $.widget('mage.tabs', $.ui.tabs, {
        options: {
            ajaxOptions: {
                data: {
                    isAjax: true,
                    form_key: FORM_KEY
                }
            },
            spinner: false,
            load: function(event, ui) {
                $('body').trigger('processStop');
                $(ui.tab).prop('href', '#' + $(ui.panel).prop('id'));
            },
            beforeLoad: function() {$('body').trigger('processStart');}
        },
        _create: function() {
            var activeIndex = this._getList().find("> li > a[href]").index($('#' + this.options.active));
            this.options.active = activeIndex >= 0 ? activeIndex : 0;
            this._super();
            this._moveTabsInDestination(this.panels);
            this._bindShadowTabs();
            this._bindOnInvalid();
            this._bindOnBeforeSubmit();
            this._bindContentChange();
        },
        activeAnchor: function() {
            return this.anchors.eq(this.option("active"));
        },
        _bindContentChange: function(){
            $.each(this.panels, $.proxy(function(i, panel) {
                $(panel).on('changed', $.proxy(function() {
                    this.anchors.eq(i).addClass('changed');
                }, this));
            }, this));
        },
        _bindOnInvalid: function() {
            $.each(this.tabs, $.proxy(function(i, tab) {
                this._getPanelForTab(tab)
                    .on('highlight.validate', $.proxy(function() {
                        $(this.anchors.eq(i)).addClass('error').find('.error').show();
                    }, this))
                    .on('focusin', $.proxy(function() {
                        this.option("active", i);
                    }, this));
            }, this));
        },
        _bindOnBeforeSubmit: function() {
            $('#' + this.options.destinationID).on('beforeSubmit', $.proxy(function(e, data) {
                var tabsIdValue = this.anchors.eq(this.option('active')).prop('id');
                if (this.options.tabsBlockPrefix) {
                    if (this.anchors.eq(this.option('active')).is('[id*="' + this.options.tabsBlockPrefix + '"]')) {
                        tabsIdValue = tabsIdValue.substr(this.options.tabsBlockPrefix.length);
                    }
                }
                $(this.anchors).removeClass('error');
                var options = {action: {args: {}}};
                options.action.args[this.options.tabIdArgument || 'tab'] = tabsIdValue;
                data = data ? $.extend(data, options) : options;
            }, this));
        },
        _bindShadowTabs: function(){
            var anchors = this.anchors,
                shadowTabs = this.options.shadowTabs,
                tabs = this.tabs;

            anchors.each($.proxy(function(i, anchor) {
                var anchorId = $(anchor).prop('id');
                if(shadowTabs[anchorId]) {
                    $(anchor).parents('li').on('click', $.proxy(function(e){
                        $.each(shadowTabs[anchorId], $.proxy(function(i, id){
                            this.load($(tabs).index($('#' + id).parents('li')), {});
                        }, this));
                    }, this));
                }
            }, this));
        },
        _getPanelForTab: function(tab) {
            var panel = this._super(tab);
            if (!panel.length) {
                var id = $(tab).attr("aria-controls");
                panel = $('#' + this.options.destinationID).find(this._sanitizeSelector( "#" + id ));
            }
            return panel;
        },
        _moveTabsInDestination: function(panels) {
            if(this.options.destinationID && !panels.parents('#' + this.options.destinationID).length) {
                panels
                    .appendTo('#' + this.options.destinationID)
                    .each($.proxy(function(i, panel) {
                        $(panel).trigger('move.tabs', this.anchors.eq(i));
                    }, this));
            }
        },
        _toggle: function(event, eventData) {
            this._moveTabsInDestination(eventData.newPanel);
            var anchor = $(eventData.newTab).find('a');
            if ($(eventData.newTab).find('a').data().tabType === 'link') {
                setLocation(anchor.prop('href'));
            } else {
                this._superApply(arguments);
            }
        }
    });
})(jQuery);
