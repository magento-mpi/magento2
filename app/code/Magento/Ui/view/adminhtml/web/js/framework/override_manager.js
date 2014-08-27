define(['jquery'], function ($) {

  return {
    remove: function (part, into, by) {
      var toRemove = $(into).find('[data-part="' + part + '"]');
      toRemove.remove();
      $(by).remove();
    },

    replace: function (part, into, by) {
      var toReplace = _.last(by);
      var toBeReplaced = $(into).find('[data-part="' + part + '"]');

      toBeReplaced.replaceWith(toReplace);
    },

    update: function (part, into, by) {
      var toBeUpdated = $(into).find('[data-part="' + part + '"]').get(0);
      var toUpdate = _.last(by);

      var attributes = toUpdate.attributes;
      var value, name;

      _.each(attributes, function (attr) {
        value = attr.value;
        name = attr.name;

        if (attr.name.indexOf('data-part') !== -1) {
          return;
        }

        $(toBeUpdated).attr(name, value);
      });

      $(toUpdate).remove();
    },

    prepend: function (part, into, by) {
      var actionNode = $(_.last(by));
      var toPrepend = actionNode.children();
      var toBePrepended = $(into).find('[data-part="' + part + '"]');

      toBePrepended.prepend(toPrepend);
      actionNode.remove();
    },

    append: function (part, into, by) {
      var actionNode = $(_.last(by));
      var toAppend = actionNode.children();
      var toBeAppended = $(into).find('[data-part="' + part + '"]');

      toBeAppended.append(toAppend);
      actionNode.remove();
    },

    getActions: function () {
      return 'replace remove update append prepend'.split(' ');
    }
  };
});