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
 * Enterprise banner catalog rule model
 *
 * @method Enterprise_Banner_Model_Resource_Catalogrule _getResource()
 * @method Enterprise_Banner_Model_Resource_Catalogrule getResource()
 * @method int getBannerId()
 * @method Enterprise_Banner_Model_Catalogrule setBannerId(int $value)
 * @method int getRuleId()
 * @method Enterprise_Banner_Model_Catalogrule setRuleId(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Catalogrule extends Magento_Core_Model_Abstract
{
    /**
     * Initialize promo catalog price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Banner_Model_Resource_Catalogrule');
    }
}
