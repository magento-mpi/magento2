<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import proxy product resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model\Import\Proxy\Product;

class Resource extends \Magento\Catalog\Model\Resource\Product
{
    /**
     * Product to category table.
     *
     * @return string
     */
    public function getProductCategoryTable()
    {
        return $this->_productCategoryTable;
    }

    /**
     * Product to website table.
     *
     * @return string
     */
    public function getProductWebsiteTable()
    {
        return $this->_productWebsiteTable;
    }
}
