define(['Magento_Ui/js/framework/utils'], function (utils) {

  var Class = function () {
    var mixin, i, field;

    var MIXIN_INIT_FN = 'setUp';

    if (this.mixins) {

      for (i = 0; i < this.mixins.length; i++) {
        mixin = this.mixins[i];

        for (field in mixin) {
          if (mixin.hasOwnProperty(field)) {
            if (field !== MIXIN_INIT_FN) {
              this.constructor.prototype[field] = mixin[field];
            }
          }
        }

        if (MIXIN_INIT_FN in mixin) {
          mixin[MIXIN_INIT_FN].call(this);  
        }
      }
    }

    if (this.initialize) {
      this.initialize.apply(this, arguments);
    }
  }

  Class.extend = utils.protoExtend;

  return Class;
})