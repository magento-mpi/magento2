<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Adminhtml\Customer\Edit\Tab\Wishlist;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Grid on Wishlist tab in customer details on backend
 */
class Grid extends ParentGrid
{
    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = [
        'product_name' => [
            'selector' => 'input[name="product_name"]'
        ],
    ];

    /**
     * Delete link selector
     *
     * @var string
     */
    protected $deleteLink = 'a[onclick*="removeItem"]';

    /**
     * Search item and delete it
     *
     * @param array $filter
     * @return void
     * @throws \Exception
     */
    public function searchAndDelete(array $filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        $rowItem->find($this->deleteLink, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->acceptAlert();
    }
}
