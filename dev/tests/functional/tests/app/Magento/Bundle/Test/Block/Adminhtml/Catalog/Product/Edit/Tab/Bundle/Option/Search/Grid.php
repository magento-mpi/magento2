<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * 'Add Products to Bundle Option' grid
 *
 * @package Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search
 */
class Grid extends GridInterface
{
    /**
     * 'Add Selected Products' button
     *
     * @var string
     */
    private $addProducts;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => 'input[name=name]',
            'sku' => 'input[name=sku]',
        );
        $this->addProducts = 'button.add';
        $this->selectItem = 'tbody tr .col-id';
    }

    /**
     * Press 'Add Selected Products' button
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts, Locator::SELECTOR_CSS)->click();
    }
}
