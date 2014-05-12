<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import proxy product model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model\Import\Proxy;

class Product extends \Magento\Catalog\Model\Product
{
    /**
     * DO NOT Initialize resources.
     *
     * @return void
     */
    protected function _construct()
    {
    }
}
