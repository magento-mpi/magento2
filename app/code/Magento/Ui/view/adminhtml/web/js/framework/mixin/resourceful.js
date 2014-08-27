define(function () {
  return {
    setUp: function () {
      this.def('isLocked', false);
    },

    lock: function () {
      this.isLocked(true);

      return this;
    },

    unlock: function () {
      this.isLocked(false);

      return this;
    }
  };
});