<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Magento\Backend\Test\Block\Template;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Adminhtml sales order create items block
 */
class Items extends Block
{
    /**
     * 'Add Products' button
     *
     * @var string
     */
    protected $addProducts = "//button[span='Add Products']";

    /**
     * Item product
     *
     * @var string
     */
    protected $itemProduct = '//tr[td//*[normalize-space(text())="%s"]]';

    /**
     * Product names
     *
     * @var string
     */
    protected $productNames = '//td[@class="col-product"]//span';

    /**
     * Selector for template block.
     *
     * @var string
     */
    protected $template = './ancestor::body';

    /**
     * Click 'Add Products' button
     *
     * @return void
     */
    public function clickAddProducts()
    {
        $element = $this->_rootElement;
        $selector = $this->addProducts;
        $this->_rootElement->waitUntil(
            function () use ($element, $selector) {
                $addProductsButton = $element->find($selector, Locator::SELECTOR_XPATH);
                return $addProductsButton->isVisible() ? true : null;
            }
        );
        $this->_rootElement->find($this->addProducts, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get item product block
     *
     * @param string $name
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Items\ItemProduct
     */
    public function getItemProductByName($name)
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\Items\ItemProduct',
            ['element' => $this->_rootElement->find(sprintf($this->itemProduct, $name), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get products data by fields from items ordered grid.
     *
     * @param array $fields
     * @return array
     */
    public function getProductsDataByFields($fields)
    {
        $this->getTemplateBlock()->waitLoader();
        $this->_rootElement->click();
        $products = $this->_rootElement->find($this->productNames, Locator::SELECTOR_XPATH)->getElements();
        $pageData = [];
        foreach ($products as $product) {
            $pageData[] = $this->getItemProductByName($product->getText())->getCheckoutData($fields);
        }

        return $pageData;
    }

    /**
     * Get template block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Magento\Backend\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->template, Locator::SELECTOR_XPATH)]
        );
    }
}
