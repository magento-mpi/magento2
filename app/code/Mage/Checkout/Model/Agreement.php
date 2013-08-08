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
 * @method Mage_Checkout_Model_Resource_Agreement _getResource()
 * @method Mage_Checkout_Model_Resource_Agreement getResource()
 * @method string getName()
 * @method Mage_Checkout_Model_Agreement setName(string $value)
 * @method string getContent()
 * @method Mage_Checkout_Model_Agreement setContent(string $value)
 * @method string getContentHeight()
 * @method Mage_Checkout_Model_Agreement setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method Mage_Checkout_Model_Agreement setCheckboxText(string $value)
 * @method int getIsActive()
 * @method Mage_Checkout_Model_Agreement setIsActive(int $value)
 * @method int getIsHtml()
 * @method Mage_Checkout_Model_Agreement setIsHtml(int $value)
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Agreement extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Checkout_Model_Resource_Agreement');
    }
}
