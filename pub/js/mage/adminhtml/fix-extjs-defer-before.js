/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*@cc_on
// code only for IE7 when ExtJs overwrite "defer" function in PrototypeJs
(function(){
    var last = null;
    var ie7 = @if(@_jscript_version==5.7) 1 @end + 0;
    var ie8 = @if(@_jscript_version==5.8) 1 @end + 0;
    if (ie7 || ie8) {
    if (ie7) {
        var eDefer = Function.prototype.defer;
        Function.prototype.defer = function() {
            // prevent throw stack overflow exception
            if (last !== this) {
                last = this;
                eDefer.apply(last, arguments);
            }
            return this;
        };
    }
})();
@*/
