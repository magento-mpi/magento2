<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile information tab
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View\Tab;

class Info
    extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Profile Information');
    }

    /**
     * Also label getter :)
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
