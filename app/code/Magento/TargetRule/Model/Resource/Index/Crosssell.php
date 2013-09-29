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
 * TargetRule Crosssell Catalog Product List Index Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\TargetRule\Model\Resource\Index;

class Crosssell extends \Magento\TargetRule\Model\Resource\Index\AbstractIndex
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType     = \Magento\TargetRule\Model\Rule::CROSS_SELLS;

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule_index_crosssell', 'entity_id');
    }
}
