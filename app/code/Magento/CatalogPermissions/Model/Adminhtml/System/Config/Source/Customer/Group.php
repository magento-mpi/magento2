<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for customer group multiselect
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Customer;

class Group
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('Magento\Customer\Model\Resource\Group\Collection')
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
