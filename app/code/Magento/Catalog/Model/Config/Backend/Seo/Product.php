<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Backend\Seo;

use Magento\Framework\App\Config\Value;

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
