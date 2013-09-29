<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Model\Config\Backend\Seo;

class Product extends \Magento\Core\Model\Config\Value
{
    /**
     * Refresh category url rewrites if configuration was changed
     *
     * @return \Magento\Catalog\Model\Config\Backend\Seo\Product
     */
    protected function _afterSave()
    {
        return $this;
    }
}
