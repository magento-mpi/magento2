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
    implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory
     */
    public function __construct(\Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory)
    {
        $this->_groupCollFactory = $groupCollFactory;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollFactory->create()
                ->loadData()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
