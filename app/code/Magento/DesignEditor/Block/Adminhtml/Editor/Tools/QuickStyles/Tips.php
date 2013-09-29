<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\QuickStyles;

/**
 * Block that renders JS tab
 *
 * @method \Magento\Core\Model\Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Tips
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\QuickStyles\AbstractTab
{
    /**
     * Tab form HTML identifier
     *
     * @var string
     */
    protected $_formId = 'quick-styles-form-tips';

    /**
     * Controls group which will be rendered on the tab form
     *
     * @var string
     */
    protected $_tab = 'tips';
}
