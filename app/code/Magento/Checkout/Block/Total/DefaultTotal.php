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
namespace Magento\Checkout\Block\Total;

class DefaultTotal extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Checkout::total/default.phtml';

    /**
     * @var \Magento\Core\Model\Store
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
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->_store;
    }
}
