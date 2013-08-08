<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @method Mage_Sales_Model_Resource_Order_Tax _getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax getResource()
 * @method int getOrderId()
 * @method Mage_Sales_Model_Order_Tax setOrderId(int $value)
 * @method string getCode()
 * @method Mage_Sales_Model_Order_Tax setCode(string $value)
 * @method string getTitle()
 * @method Mage_Sales_Model_Order_Tax setTitle(string $value)
 * @method float getPercent()
 * @method Mage_Sales_Model_Order_Tax setPercent(float $value)
 * @method float getAmount()
 * @method Mage_Sales_Model_Order_Tax setAmount(float $value)
 * @method int getPriority()
 * @method Mage_Sales_Model_Order_Tax setPriority(int $value)
 * @method int getPosition()
 * @method Mage_Sales_Model_Order_Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method Mage_Sales_Model_Order_Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method Mage_Sales_Model_Order_Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method Mage_Sales_Model_Order_Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method Mage_Sales_Model_Order_Tax setHidden(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Tax extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Tax');
    }
}
