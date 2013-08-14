<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise banner sales rule model
 *
 * @method Enterprise_Banner_Model_Resource_Salesrule _getResource()
 * @method Enterprise_Banner_Model_Resource_Salesrule getResource()
 * @method int getBannerId()
 * @method Enterprise_Banner_Model_Salesrule setBannerId(int $value)
 * @method int getRuleId()
 * @method Enterprise_Banner_Model_Salesrule setRuleId(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Salesrule extends Magento_Core_Model_Abstract
{
    /**
     * Initialize promo shopping cart price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Banner_Model_Resource_Salesrule');
    }
}
