<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfries
 * Date: 11/14/13
 * Time: 12:13 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Fixture\Product;

class Upsell extends Tab {

    /**
     * Tab where bundle options section is placed
     */
    const GROUP_UPSELL = 'product_info_tabs_upsell';

    /**
     * Catalog product grid on backend
     *
     * @var \Magento\Catalog\Test\Block\Backend\ProductUpsellGrid
     */
   private $productEditGrid;

    /**
     * Open bundle options section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        // @var Mtf\Client\Element
        $element->find(static::GROUP_UPSELL, Locator::SELECTOR_ID)->click();

        //$this->productEditGrid = Factory::getBlockFactory()->getMagentoCatalogBackendProductEditGrid(
    }

    /**
     * Get bundle options block
     *
     * @param Element $context
     * @return void
     */
    public function setSearchElementsBlock(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;

        $element->find('up_sell_product_grid_filter_entity_id', Locator::SELECTOR_ID)->setValue('999');
    }

    /**
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @param array $upsellProducts
     */
    public function addUpsellProducts($product, $upsellProducts)
    {
        /** @var Product $upsellProduct */
        foreach ($upsellProducts as $upsellProduct) {
            // locate the edit page.
            $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
            $productEditPage->open(array('id' => $product->getProductId()));
            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Save"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->directToUpsellTab();

            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);

            $productEditPage->getProductUpsellGrid()->searchAndSelect(
                array('name' => $upsellProduct->getProductName()));
            $productEditPage->getProductBlockForm()->save($product);
            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);
            //Verifying
//            $this->assertSuccessMessage("You saved the product.", $productEditPage);
        }
    }
}