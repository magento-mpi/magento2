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
 * Banner Salesrule Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Resource_Salesrule extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize banner sales rule resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_banner_salesrule', 'rule_id');
    }
}
