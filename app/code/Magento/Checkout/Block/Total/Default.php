<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Default Total Row Renderer
 */
class Magento_Checkout_Block_Total_Default extends Magento_Checkout_Block_Cart_Totals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Checkout::total/default.phtml';

    /**
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    protected function _construct()
    {
        parent::_construct();
        $this->_store = $this->_storeManager->getStore();
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

    /**
     * @param $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->setData('total', $total);
        if ($total->getAddress()) {
            $this->_store = $total->getAddress()->getQuote()->getStore();
        }
        return $this;
    }

    /**
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }
}
