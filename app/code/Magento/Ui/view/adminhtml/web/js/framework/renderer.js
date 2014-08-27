define([
  'Magento_Ui/js/framework/loader',
  'Magento_Ui/js/framework/override_manager',
  'jquery',
  '_'
], function (loader, overrides, $, _) {

  return {

    render: function (template) {
      var isRendered = $.Deferred();

      this._bind();

      loader
        .loadTemplate(template)
        .then(this._parse)
        .done(isRendered.resolve.bind(isRendered));

      return isRendered.promise();
    },

    _bind: function () {
      _.bindAll(this, '_parse');
    },

    _parse: function (html) {
      var 
        templatePath,
        template,
        renderedExtendPoints = [];

      template = wrap($(html));

      var extendNodes = $(template).find('[data-template-extend]');

      _.each(extendNodes, function (node) {

        templatePath = $(node).attr('data-template-extend');
        renderedExtendPoints.push(this.render(templatePath));

      }, this);

      return waitFor(renderedExtendPoints).then(function () {
        var container, wrappedNodes, newParts = [];

        _.each(arguments, function (nodes, i) {

          wrappedNodes = wrap(nodes);
          container = extendNodes[i];
          newParts = this._extractPartsFrom(container);

          if (!_.isEmpty(newParts)) {
            container.empty();
          }
          
          this._overridePartsOf(wrappedNodes).by(newParts).appendTo(container);

        }, this);

        return toArray($(template).children());
      }.bind(this));
    },

    _extractPartsFrom: function(node) {
      var parts = {}, actionNodes, partSelector, target, action;

      _.each(overrides.getActions(), function (partAction) {
        partSelector = '[data-part-' + partAction + ']';
        actionNodes  = $(node).find(partSelector);

        _.each(actionNodes, function (node) {
          target = $(node).attr('data-part-' + partAction);
          
          if (!parts[target]) {
            parts[target] = {};
          }

          target = parts[target];

          if (!target[partAction]) {
            target[partAction] = [];
          }

          target[partAction].push(node);
        });
      });

      return parts;
    },

    _overridePartsOf: function (targetNode) {
      return {
        by: function (newParts) {
          _.each(newParts, function (actions, part) {
            _.each(actions, function (nodes, action) {

              overrides[action](part, targetNode, nodes);

            });
          });

          return {
            appendTo: function (container) {
              $(container).append($(targetNode).children());
            }
          }
        }
      }
    }
  };

  function wrap(collection) {
    return $('<div />').append(collection);
  }

  function toArray(arrayLikeObject) {
    return Array.prototype.slice.call(arrayLikeObject, 0);
  }

  function waitFor(promises) {
    return $.when.apply(this, promises);
  }
});