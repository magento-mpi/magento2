/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_'
], function(_) {

    function extend( protoProps ){
        var parent = this,
            child,
            args,
            hasConstructor;

        protoProps      = protoProps || {};
        hasConstructor  = protoProps.hasOwnProperty('constructor');

        child = hasConstructor ?
            protoProps.constructor :
            function() {
                return parent.apply(this, arguments);
            };

        child.prototype = Object.create( parent.prototype );
        child.prototype.constructor = child;

        args = [child.prototype];

        args.push.apply(args, arguments);

        _.extend.apply(_, args);

        child.extend = extend;
        child.__super__ = parent.prototype;

        return child;
    }

    function Class() {
        this.initialize.apply(this, arguments);
    }

    Class.prototype.initialize = function(){};

    Class.extend = extend;

    return Class;
});