<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Adminhtml\Customer\Edit\Tab\Wishlist;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Grid on Wishlist tab
 */
class Grid extends GridInterface
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
    protected $deleteLink = '//a[contains(.,"Delete")]';

    /**
     * Search item and delete it
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndDelete(array $filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->deleteLink, Locator::SELECTOR_XPATH)->click();
            $this->waitForElement();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
