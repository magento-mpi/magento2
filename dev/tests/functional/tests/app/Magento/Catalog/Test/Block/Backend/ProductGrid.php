<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class ProductGrid
 * Backend catalog product grid
 *
 * @package Magento\Catalog\Test\Block
 */
class ProductGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => '#productGrid_product_filter_name',
            'sku' => '#productGrid_product_filter_sku'
        );
    }

    /**
     * Click "Select All" and submit Delete action
     */
    public function deleteAll()
    {
        $this->_rootElement
            ->find('//*[@id="productGrid_massaction"]//a[text()="Select All"]', Locator::SELECTOR_XPATH)
            ->click();
        $this->_rootElement
            ->find('productGrid_massaction-select', Locator::SELECTOR_ID, 'select')
            ->setValue('Delete');
        $this->_rootElement
            ->find('productGrid_massaction-form', Locator::SELECTOR_ID)
            ->find('[title=Submit]', Locator::SELECTOR_CSS)
            ->click();
        $this->_rootElement->acceptAlert();
    }
}
