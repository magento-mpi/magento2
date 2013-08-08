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
 * Banner Catalogrule Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Resource_Catalogrule extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize banner catalog rule resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_banner_catalogrule', 'rule_id');
    }
}
