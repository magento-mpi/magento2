<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Tax_Model_Resource_Sales_Order_Tax _getResource()
 * @method Magento_Tax_Model_Resource_Sales_Order_Tax getResource()
 * @method int getOrderId()
 * @method Magento_Tax_Model_Sales_Order_Tax setOrderId(int $value)
 * @method string getCode()
 * @method Magento_Tax_Model_Sales_Order_Tax setCode(string $value)
 * @method string getTitle()
 * @method Magento_Tax_Model_Sales_Order_Tax setTitle(string $value)
 * @method float getPercent()
 * @method Magento_Tax_Model_Sales_Order_Tax setPercent(float $value)
 * @method float getAmount()
 * @method Magento_Tax_Model_Sales_Order_Tax setAmount(float $value)
 * @method int getPriority()
 * @method Magento_Tax_Model_Sales_Order_Tax setPriority(int $value)
 * @method int getPosition()
 * @method Magento_Tax_Model_Sales_Order_Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method Magento_Tax_Model_Sales_Order_Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method Magento_Tax_Model_Sales_Order_Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method Magento_Tax_Model_Sales_Order_Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method Magento_Tax_Model_Sales_Order_Tax setHidden(int $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Sales_Order_Tax extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_Sales_Order_Tax');
    }
}
