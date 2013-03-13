<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Customers Tags grid collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Customer_Grid_Collection extends Mage_Tag_Model_Resource_Reports_Customer_Collection
{
    /**
     * @return Mage_Tag_Model_Resource_Customer_Collection|Mage_Tag_Model_Resource_Reports_Customer_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)->addGroupByCustomer()->addTagedCount();
        return $this;
    }
}
