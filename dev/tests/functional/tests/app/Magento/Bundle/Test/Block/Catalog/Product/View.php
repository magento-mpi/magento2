<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product;

use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product\View as ParentView;
use Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle;

/**
 * Class View
 * Bundle product view block on the product page
 */
class View extends ParentView
{
    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '//*[@id="product-options-wrapper"]//fieldset[contains(@class,"bundle")]';

    /**
     * Get bundle options block
     *
     * @return Bundle
     */
    public function getBundleBlock()
    {
        return $this->blockFactory->create(
            'Magento\Bundle\Test\Block\Catalog\Product\View\Type\Bundle',
            ['element' => $this->_rootElement->find($this->bundleBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
