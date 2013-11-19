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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Fixture\Product;

class Upsell extends Tab {

    /**
     * Tab where upsells section is placed
     */
    const GROUP_UPSELL = 'product_info_tabs_upsell';

    /**
     * Open upsells section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        // @var Mtf\Client\Element
        $element->find(static::GROUP_UPSELL, Locator::SELECTOR_ID)->click();
    }

    /**
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @param array $upsellProducts
     */
    public static function addUpsellProducts($product, $upsellProducts)
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
            $productEditPage->getMessagesBlock()->assertSuccessMessage();
        }
    }
}