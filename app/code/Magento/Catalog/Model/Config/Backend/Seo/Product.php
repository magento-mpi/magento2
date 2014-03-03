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

use Magento\Core\Model\Config\Value;

class Product extends Value
{
    /**
     * Refresh category url rewrites if configuration was changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        return $this;
    }
}
