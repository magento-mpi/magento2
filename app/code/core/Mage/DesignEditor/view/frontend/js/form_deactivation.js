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
     * Widget Form Deactivation
     */
    $.widget('vde.vde_formDeactivation', {
        _create: function () {
            this._initFormDeactivation();
        },
        _initFormDeactivation: function () {
            $('form').submit( function(e){
                e.preventDefault();
            });
        }
    });

    $(document).ready(function( ){
        $(window).vde_formDeactivation();
    });
})( jQuery );
