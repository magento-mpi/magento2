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
 * @method Magento_Checkout_Model_Resource_Agreement _getResource()
 * @method Magento_Checkout_Model_Resource_Agreement getResource()
 * @method string getName()
 * @method Magento_Checkout_Model_Agreement setName(string $value)
 * @method string getContent()
 * @method Magento_Checkout_Model_Agreement setContent(string $value)
 * @method string getContentHeight()
 * @method Magento_Checkout_Model_Agreement setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method Magento_Checkout_Model_Agreement setCheckboxText(string $value)
 * @method int getIsActive()
 * @method Magento_Checkout_Model_Agreement setIsActive(int $value)
 * @method int getIsHtml()
 * @method Magento_Checkout_Model_Agreement setIsHtml(int $value)
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Agreement extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Checkout_Model_Resource_Agreement');
    }
}
