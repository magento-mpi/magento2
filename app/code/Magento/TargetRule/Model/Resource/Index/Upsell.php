<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Upsell Catalog Product List Index Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Resource_Index_Upsell extends Magento_TargetRule_Model_Resource_Index_Abstract
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType     = Magento_TargetRule_Model_Rule::UP_SELLS;

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule_index_upsell', 'entity_id');
    }
}
