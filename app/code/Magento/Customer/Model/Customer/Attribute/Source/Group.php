<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Source;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupsFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory
    ) {
        $this->_groupsFactory = $groupsFactory;
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_getCustomerGroupsCollection()->setRealGroupsFilter()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \Magento\Customer\Model\Resource\Group\Collection
     */
    protected function _getCustomerGroupsCollection()
    {
        return $this->_groupsFactory->create();
    }
}
