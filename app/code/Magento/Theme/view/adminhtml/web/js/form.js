/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */
function parentThemeOnChange(selected, defaultsById) {
    var statusBar = $$('.tab-item-link')[0];
    var isChanged = statusBar.hasClassName('changed');
    if (!isChanged) {
        var defaults = defaultsById[selected];
        $('theme_title').value = defaults.theme_title;
    }
}
