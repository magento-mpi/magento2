/*@cc_on
(function(){
    var eDefer = Function.prototype.defer;
    Function.prototype.defer = function(a1, a2, a3, a4) {
        eDefer(this, a1 || 50, a2, a3, a4);
    };
})();
@*/