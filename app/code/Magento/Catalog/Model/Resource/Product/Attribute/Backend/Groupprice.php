<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

use Magento\Catalog\Model\Resource\Product\Attribute\Backend\Groupprice\AbstractGroupprice;

/**
 * Catalog product group price backend attribute model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Groupprice
    extends AbstractGroupprice
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity_group_price', 'value_id');
    }
}
