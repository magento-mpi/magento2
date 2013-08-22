<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @method Magento_Sales_Model_Resource_Order_Tax _getResource()
 * @method Magento_Sales_Model_Resource_Order_Tax getResource()
 * @method int getOrderId()
 * @method Magento_Sales_Model_Order_Tax setOrderId(int $value)
 * @method string getCode()
 * @method Magento_Sales_Model_Order_Tax setCode(string $value)
 * @method string getTitle()
 * @method Magento_Sales_Model_Order_Tax setTitle(string $value)
 * @method float getPercent()
 * @method Magento_Sales_Model_Order_Tax setPercent(float $value)
 * @method float getAmount()
 * @method Magento_Sales_Model_Order_Tax setAmount(float $value)
 * @method int getPriority()
 * @method Magento_Sales_Model_Order_Tax setPriority(int $value)
 * @method int getPosition()
 * @method Magento_Sales_Model_Order_Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method Magento_Sales_Model_Order_Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method Magento_Sales_Model_Order_Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method Magento_Sales_Model_Order_Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method Magento_Sales_Model_Order_Tax setHidden(int $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Tax extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Tax');
    }
}
