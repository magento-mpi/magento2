<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Magento\Catalog\Test\Constraint\AssertProductInGrid;

/**
 * Assert that downloadable product is present in products grid.
 */
class AssertDownloadableProductInGrid extends AssertProductInGrid
{
    /**
     * Get downloadable product type.
     *
     * @return string
     */
    protected function getProductType()
    {
        $isVirtual = $this->product->getIsVirtual();
        if ($isVirtual == 'No') {
            return 'Simple Product';
        }

        $config = $this->product->getDataConfig();
        return ucfirst($config['type_id']) . ' Product';
    }
}
