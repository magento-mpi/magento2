<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Shopingcart Products Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Shopcart_Product_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Join fields
     *
     * @return Mage_Reports_Model_Resource_Shopcart_Product_Collection
     */
    protected function _joinFields()
    {
        parent::_joinFields();
        $this->addAttributeToSelect('price')
            ->addCartsCount()
            ->addOrdersCount();

        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param strin $to
     * @return Mage_Reports_Model_Resource_Shopcart_Product_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->getSelect()->reset();
        return $this;
    }
}
