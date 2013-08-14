<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display gift cards applied to order
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_GiftCards extends Magento_Backend_Block_Template
{
    /**
     * Order
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    /**
     * Cache for gift card information
     *
     * @var array
     */
    protected $_cards = array();

    /**
     * Initializes template
     * @see Magento_Core_Block_Template::_construct()
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::gift_cards.phtml');
    }

    /**
     * Set order
     *
     * @param Magento_Sales_Model_Order $order
     * @return Saas_PrintedTemplate_Block_GiftCards Self
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;

        return $this;
    }

    /**
     * Returns array of the order's applied gift cards
     *
     * @return array array(1) {
     *                 [0]=>
     *                 array(4) {
     *                   ["i"]=>
     *                   string(1) "1"                // ID
     *                   ["c"]=>
     *                   string(12) "00XDW1KF6MGP"    // Code
     *                   ["a"]=>
     *                   float(329.74)                // Amount
     *                   ["ba"]=>
     *                   float(329.74)                // Base amount
     *                 }
     *               }
     *
     */
    public function getCards()
    {
        if (!$this->_cards && $this->_order) {
            $cards = @unserialize($this->_order->getGiftCards());
            if (is_array($cards)) {
                $this->_cards = $cards;
            }
        }

        return $this->_cards;
    }

    /**
     * If cards array is empty return empty string.
     *
     * @return string HTML
     * @see Magento_Core_Block_Template::_toHtml()
     */
    protected function _toHtml()
    {
        return count($this->getCards()) ? parent::_toHtml() : '';
    }


    /**
     * Return the order used by this block
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
}
