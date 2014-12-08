<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopingcart Products Report collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Shopcart\Product;

class Collection extends \Magento\Reports\Model\Resource\Product\Collection
{
    /**
     * Join fields
     *
     * @return $this
     */
    protected function _joinFields()
    {
        parent::_joinFields();
        $this->addAttributeToSelect('price')->addCartsCount()->addOrdersCount();

        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->getSelect()->reset();
        return $this;
    }
}
