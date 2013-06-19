/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */
function parentThemeOnChange(selected, defaultsById) {
    var statusBar = $$('.tab-item-link')[0];
    var isChanged = statusBar.hasClassName('changed');
    if (!isChanged) {
        var defaults = defaultsById[selected];
        $('theme_title').value = defaults.theme_title;
        $('magento_version_from').value = defaults.magento_version_from;
        $('magento_version_to').value = defaults.magento_version_to;
    }
}
