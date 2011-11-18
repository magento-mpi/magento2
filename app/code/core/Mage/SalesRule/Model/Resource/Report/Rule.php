<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rule report resource model
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Report_Rule extends Mage_Reports_Model_Resource_Report_Abstract
{
    /**
     * Resource Report Rule constructor
     *
     */
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Resource_Report_Rule
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('Mage_SalesRule_Model_Resource_Report_Rule_Createdat')->aggregate($from, $to);
        Mage::getResourceModel('Mage_SalesRule_Model_Resource_Report_Rule_Updatedat')->aggregate($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE);

        return $this;
    }
}
