<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise banner sales rule model
 *
 * @method Magento_Banner_Model_Resource_Salesrule _getResource()
 * @method Magento_Banner_Model_Resource_Salesrule getResource()
 * @method int getBannerId()
 * @method Magento_Banner_Model_Salesrule setBannerId(int $value)
 * @method int getRuleId()
 * @method Magento_Banner_Model_Salesrule setRuleId(int $value)
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Model_Salesrule extends Magento_Core_Model_Abstract
{
    /**
     * Initialize promo shopping cart price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Banner_Model_Resource_Salesrule');
    }
}
