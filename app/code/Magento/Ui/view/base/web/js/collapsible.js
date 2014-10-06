/**
 * Created by sivashchenko on 10/3/2014.
 */
define([
    "underscore",
    "Magento_Ui/js/lib/ko/scope",
    "Magento_Ui/js/lib/registry/registry",
    "jquery"
], function(_, Scope, registry, $){
    var defaults = {
        'opened': true,
        'isLoaded': true
    };

    var Collapsible = Scope.extend({
        initialize: function(config, el){
            _.extend(this, defaults, config);

            this.el = el;

            if (this.source) {
                this.isLoaded = false;
            }

            this.initObservable();
        },

        initObservable: function(){
            this.observe({
                'opened': this.opened
            });

            return this;
        },

        load: function () {
            var self = this,
                container,
                targetContent;

            return $.get(this.source, {
                form_key: FORM_KEY,
                container: this.name,
                name: 'demo_form',
                component: 'form'
            }).then(function (html) {
                self.isLoaded   = true;
                container       = $(this.el).find('[data-role="content"]');
                targetContent   = $(html).find('[data-role="content"]').html();
                container.html(targetContent);

                return true;
            });
        },

        show: function () {
            this.isLoaded ? this.opened(true) : this.load().done(this.opened.bind(this));
        },

        hide: function () {
            this.opened(false);
        },

        toggle: function(){
            var opened = this.opened();

            opened ? this.hide() : this.show();
        }
    });

    return function(el, config){
        registry.set(config.name, new Collapsible(config, el));
    }
});