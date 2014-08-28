define([
    './utils'
], function(utils) {

    function Class() {
        this.initialize.apply(this, arguments);
    }

    Class.prototype.initialize = function(){};

    Class.extend = utils.protoExtend;

    return Class;
});