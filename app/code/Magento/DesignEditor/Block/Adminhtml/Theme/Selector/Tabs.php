<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme selectors tabs container
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize tab
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('theme_selector_tabs');
        $this->setDestElementId('theme_selector');
        $this->setIsHoriz(true);
    }

    /**
     * Add content container to template
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . '<div id="' . $this->getDestElementId() . '" class="theme-selector"><div>' .
            $this->_getScript();
    }

    /**
     * Get additional script for tabs block
     *
     * @return string
     */
    protected function _getScript()
    {
        $script = sprintf("
            (function ($) {
                $('.themes-customizations .theme').themeControl({url: '%s'});
            })(jQuery);",
            $this->getUrl('*/*/quickEdit')
        );
        return sprintf('<script type="text/javascript">%s</script>', $script);
    }
}
