<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Page\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductView as ParentCatalogProductView;

/**
 * Class CatalogProductView
 *
 * Frontend downloadable product view page
 */
class CatalogProductView extends ParentCatalogProductView
{
    const MCA = 'downloadable/catalog/product/view';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['downloadableViewBlock'] = [
            'name' => 'downloadableViewBlock',
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\View',
            'locator' => '.product.info.main',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View
     */
    public function getDownloadableViewBlock()
    {
        return $this->getBlockInstance('downloadableViewBlock');
    }
}
