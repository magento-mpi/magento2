<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class ProductLowStock
 * Product Low Stock page
 */
class ProductLowStock extends BackendPage
{
    const MCA = 'reports/report_product/lowstock';

    protected $_blocks = [
        'lowStockGrid' => [
            'name' => 'lowStockGrid',
            'class' => 'Magento\Reports\Test\Block\Adminhtml\Product\Lowstock\Grid',
            'locator' => '#gridLowstock',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Reports\Test\Block\Adminhtml\Product\Lowstock\Grid
     */
    public function getLowStockGrid()
    {
        return $this->getBlockInstance('lowStockGrid');
    }
}
