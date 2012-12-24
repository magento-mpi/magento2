/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget Forms disabler
     */
    $.widget('vde.vde_formsDisabler', {
        _create: function () {
            this._initFormsDisabler();
        },
        _initFormsDisabler: function () {
            $('form').submit( function(e){
                e.preventDefault();
            });
        }
    });

    $(document).ready(function( ){
        $(window).vde_formsDisabler();
    });
})( jQuery );
