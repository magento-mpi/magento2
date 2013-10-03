<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Shopingcart Products Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Shopcart\Product;

class Collection extends \Magento\Reports\Model\Resource\Product\Collection
{
    /**
     * Join fields
     *
     * @return \Magento\Reports\Model\Resource\Shopcart\Product\Collection
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
     * @return \Magento\Reports\Model\Resource\Shopcart\Product\Collection
     */
    public function setDateRange($from, $to)
    {
        $this->getSelect()->reset();
        return $this;
    }
}
