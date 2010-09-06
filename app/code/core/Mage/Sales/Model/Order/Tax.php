<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @method Mage_Sales_Model_Resource_Order_Tax _getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax getResource()
 * @method Mage_Sales_Model_Order_Tax getOrderId()
 * @method int setOrderId(int $value)
 * @method Mage_Sales_Model_Order_Tax getCode()
 * @method string setCode(string $value)
 * @method Mage_Sales_Model_Order_Tax getTitle()
 * @method string setTitle(string $value)
 * @method Mage_Sales_Model_Order_Tax getPercent()
 * @method float setPercent(float $value)
 * @method Mage_Sales_Model_Order_Tax getAmount()
 * @method float setAmount(float $value)
 * @method Mage_Sales_Model_Order_Tax getPriority()
 * @method int setPriority(int $value)
 * @method Mage_Sales_Model_Order_Tax getPosition()
 * @method int setPosition(int $value)
 * @method Mage_Sales_Model_Order_Tax getBaseAmount()
 * @method float setBaseAmount(float $value)
 * @method Mage_Sales_Model_Order_Tax getProcess()
 * @method int setProcess(int $value)
 * @method Mage_Sales_Model_Order_Tax getBaseRealAmount()
 * @method float setBaseRealAmount(float $value)
 * @method Mage_Sales_Model_Order_Tax getHidden()
 * @method int setHidden(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }
}
