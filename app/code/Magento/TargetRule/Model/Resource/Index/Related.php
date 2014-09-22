<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Resource\Index;

/**
 * TargetRule Related Catalog Product List Index Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Related extends \Magento\TargetRule\Model\Resource\Index\AbstractIndex
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType = \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS;

    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule_index_related', 'product_set_id');
    }
}
