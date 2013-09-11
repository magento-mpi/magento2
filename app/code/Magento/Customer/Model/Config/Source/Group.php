<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Customer\Model\Config\Source;

class Group implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('\Magento\Customer\Model\Resource\Group\Collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
            array_unshift($this->_options, array('value'=> '', 'label'=> __('-- Please Select --')));
        }
        return $this->_options;
    }
}
