define([
    'ko',
    './observable_source',
    'Magento_Ui/js/framework/renderer',
    'm2/m2'
], function (ko, SourceFactory, Renderer, M2) {
    'use strict';

    var sources = {};

    var CustomTemplateEngine = function() {};
    var NativeTemplateEngine = ko.nativeTemplateEngine;

    CustomTemplateEngine.prototype = new NativeTemplateEngine;
    CustomTemplateEngine.prototype.constructor = CustomTemplateEngine;

    CustomTemplateEngine.prototype.makeTemplateSource = function(template) {
        var source;

        if (typeof template === 'string') {
            source = sources[template];

            if (!source) {
                source = SourceFactory.create(template);
                sources[template] = source;

                Renderer.render(template).done(function (rendered) {
                  source.nodes(rendered);
                  M2.init(rendered);
                });
            }

            return source;

        } else if ((template.nodeType == 1) || (template.nodeType == 8)) {
            return new ko.templateSources.anonymousTemplate(template);
        } else {
            throw new Error("Unknown template type: " + template);
        }
    }

    CustomTemplateEngine.prototype.renderTemplateSource = function(templateSource, bindingContext, options) {
        var nodes = templateSource.nodes();

        return ko.utils.cloneNodes(nodes);
    }

    return new CustomTemplateEngine;

});