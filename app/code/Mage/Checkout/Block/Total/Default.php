<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Default Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Total_Default extends Mage_Checkout_Block_Cart_Totals
{
    protected $_template = 'Mage_Checkout::total/default.phtml';
    protected $_store;

    protected function _construct()
    {
        $this->_store = Mage::app()->getStore();
    }

    /**
     * Get style assigned to total object
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->getTotal()->getStyle();
    }

    public function setTotal($total)
    {
        $this->setData('total', $total);
        if ($total->getAddress()) {
            $this->_store = $total->getAddress()->getQuote()->getStore();
        }
        return $this;
    }

    public function getStore()
    {
        return $this->_store;
    }
}
