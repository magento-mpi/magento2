<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Related Catalog Product List Index Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Resource_Index_Related extends Enterprise_TargetRule_Model_Resource_Index_Abstract
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType     = Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS;

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_targetrule_index_related', 'entity_id');
    }
}
