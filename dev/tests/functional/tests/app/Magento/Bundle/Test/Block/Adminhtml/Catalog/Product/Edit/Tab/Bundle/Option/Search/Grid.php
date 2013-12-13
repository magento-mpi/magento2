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
    protected $addProducts = 'button.add';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-id';

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'name' => array(
            'selector' => 'input[name=name]'
        ),
        'sku' => array(
            'selector' => 'input[name=sku]'
        ),
    );


    /**
     * Press 'Add Selected Products' button
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
    }
}
