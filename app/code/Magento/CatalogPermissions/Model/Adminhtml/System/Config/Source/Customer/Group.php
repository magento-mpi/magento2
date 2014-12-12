<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Configuration source for customer group multiselect
 *
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Customer;

use Magento\Customer\Model\Resource\Group\CollectionFactory;

class Group implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct(CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
