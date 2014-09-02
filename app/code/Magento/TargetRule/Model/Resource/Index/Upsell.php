<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Upsell Catalog Product List Index Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\TargetRule\Model\Resource\Index;

class Upsell extends \Magento\TargetRule\Model\Resource\Index\AbstractIndex
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType = \Magento\TargetRule\Model\Rule::UP_SELLS;

    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule_index_upsell', 'product_set_id');
    }
}
