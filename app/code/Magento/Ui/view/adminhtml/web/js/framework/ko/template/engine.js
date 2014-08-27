define(['ko', './observable_source', 'Magento_Ui/js/framework/renderer'], function (ko, Source, renderer) {

  var sources = {};

  var CustomTemplateEngine = function () {};

  CustomTemplateEngine.prototype = new ko.nativeTemplateEngine();
  CustomTemplateEngine.prototype.constructor = CustomTemplateEngine;

  CustomTemplateEngine.prototype.makeTemplateSource = function (template) {
    var source;

    if (typeof template === 'string') {
      source = sources[template];
      
      if (!source) {
        source = new Source(template);
        sources[template] = source;

        renderer.render(template).done(source.nodes.bind(source));
      }

      return source;

    } else if ((template.nodeType == 1) || (template.nodeType == 8)) {
      return new ko.templateSources.anonymousTemplate(template);
    } else {
      throw new Error("Unknown template type: " + template);
    }
  }

  CustomTemplateEngine.prototype.renderTemplateSource = function (templateSource, bindingContext, options) {
    var nodes = templateSource.nodes();

    return ko.utils.cloneNodes(nodes);
  }

  return new CustomTemplateEngine;
  
});