<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Customers Tags grid collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Customer_Grid_Collection extends Magento_Tag_Model_Resource_Reports_Customer_Collection
{
    /**
     * @return Magento_Tag_Model_Resource_Customer_Collection|Magento_Tag_Model_Resource_Reports_Customer_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addStatusFilter(Magento_Tag_Model_Tag::STATUS_APPROVED)->addGroupByCustomer()->addTagedCount();
        return $this;
    }
}
