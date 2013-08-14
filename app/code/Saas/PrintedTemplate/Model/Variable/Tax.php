<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for tax item variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Tax extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Static cache of orders
     *
     * @var array
     */
    protected static $_orders = array();

    /**
     * Current order locale
     *
     * @var Zend_Locale
     */
    protected $_locale;

    /**
     * Constructor
     *
     * @param mixed $value Model of tax
     */
    public function __construct($value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('tax');
    }

    /**
     * Tries to load order by parent_id, uses cache
     *
     * @return Magento_Sales_Model_Order|null
     */
    protected function _getOrder()
    {
        if ($this->_hasOrder()) {
            $orderId = $this->_value->getOrderId();
            if (!isset(self::$_orders[$orderId])) {
                self::$_orders[$orderId] = Mage::getModel('Magento_Sales_Model_Order')->load($orderId);
            }
            return self::$_orders[$orderId];
        }
    }

    /**
     * Set order for taxes
     *
     * @param Magento_Sales_Model_Order $order
     * @return Saas_PrintedTemplate_Model_Variable_Tax
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_value->setOrderId($order->getId());
        self::$_orders[$order->getId()] = $order;

        return $this;
    }

    /**
     * Check can or not load order
     *
     * @return bool
     */
    protected function _hasOrder()
    {
        return $this->_value->hasOrderId();
    }

    /**
     * Formats currency using order formatter (if has order)
     *
     * @param float $value
     * @return string
     */
    public function formatCurrency($value)
    {
        if (null !== $value) {
            return ($this->_hasOrder())
                ? $this->_getOrder()->formatPriceTxt($value)
                : parent::formatCurrency($value);
        } else {
            return '';
        }
    }
}
