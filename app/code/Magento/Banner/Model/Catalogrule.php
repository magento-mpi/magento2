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
 * Enterprise banner catalog rule model
 *
 * @method Magento_Banner_Model_Resource_Catalogrule _getResource()
 * @method Magento_Banner_Model_Resource_Catalogrule getResource()
 * @method int getBannerId()
 * @method Magento_Banner_Model_Catalogrule setBannerId(int $value)
 * @method int getRuleId()
 * @method Magento_Banner_Model_Catalogrule setRuleId(int $value)
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Model_Catalogrule extends Magento_Core_Model_Abstract
{
    /**
     * Initialize promo catalog price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Banner_Model_Resource_Catalogrule');
    }
}
