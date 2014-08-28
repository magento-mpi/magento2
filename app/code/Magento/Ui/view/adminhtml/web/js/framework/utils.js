define(['_'], function(_) {
    return {

        setValueByPathIn: function(obj, path, value) {
            var i, len, key, last, isLast;

            path = path.split('.');
            len = path.length;
            last = len - 1;

            for (i = 0; i < len; i++) {
                key = path[i];
                isLast = i === last;

                if (!isLast) {
                    if (!obj.hasOwnProperty(key)) {
                        obj[key] = {};
                    }

                    obj = obj[key];
                } else {
                    obj[key] = value;
                }

            }
        },

        protoExtend: function(protoProps) {
            var parent = this,
                child,
                args,
                hasCosntructor;

            protoProps      = protoProps || {};
            hasCosntructor  = protoProps.hasOwnProperty('constructor');

            child = hasCosntructor ?
                protoProps.constructor :
                function() {
                    return parent.apply(this, arguments);
                };

            child.prototype = Object.create( parent.prototype );
            child.prototype.constructor = child;

            args = [child.prototype];

            args.push.apply(args, arguments);

            _.extend.apply(_, args);

            child.extend = parent.extend;
            child.__super__ = parent.prototype;

            return child;
        }
    }
});