<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address Attribute General Tab Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab;

class Options
    extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\AbstractOptions
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Manage Label / Options');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
