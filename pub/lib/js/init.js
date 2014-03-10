/**
 * {license_notice}
 *
 * @category    Varien
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function() {
    var source = [
        '../../jquery/jquery.js',
        '../../mage/terms.js',
        '../../mage/dropdowns.js',
        '../../jquery/jquery.popups.js',
        '../../js/mui.js'
    ];

    for (var i=0, len=source.length; i<len; i++) {
        var script = document.createElement('script');

        script.type = 'text/javascript';
        script.async = false;
        script.src = source[i];

        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(script, s);
    }
})();
