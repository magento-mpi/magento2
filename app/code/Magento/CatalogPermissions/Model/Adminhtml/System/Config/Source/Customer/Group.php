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
    protected $_groupCollectionFactory;

    /**
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(\Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()
                ->loadData()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
