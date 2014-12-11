<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\NewRma\Tab\Items;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Grid create rma items.
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Locator item row by name.
     *
     * @var string
     */
    protected $rowByName = './/tbody/tr[./td[contains(@class,"col-product_name") and contains(text(),"%s")]]';

    /**
     * Get item row.
     *
     * @param FixtureInterface $product
     * @return Element
     */
    public function getItemRow(FixtureInterface $product)
    {
        return $this->_rootElement->find(sprintf($this->rowByName, $product->getName()), Locator::SELECTOR_XPATH);
    }
}
