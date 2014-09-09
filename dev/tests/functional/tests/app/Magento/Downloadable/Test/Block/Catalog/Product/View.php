<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Catalog\Product;

use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class View
 * Downloadable product view block on the product page
 */
class View extends \Magento\Catalog\Test\Block\Product\View
{
    /**
     * Block Downloadable links
     *
     * @var string
     */
    protected $blockDownloadableLinks = '//div[contains(@class,"field downloads")]';

    /**
     * Block Downloadable samples
     *
     * @var string
     */
    protected $blockDownloadableSamples = '//dl[contains(@class,"downloadable samples")]';

    /**
     * Get downloadable link block
     *
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View\Links
     */
    public function getDownloadableLinksBlock()
    {
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Catalog\Product\View\Links',
            [
                'element' => $this->_rootElement->find($this->blockDownloadableLinks, Locator::SELECTOR_XPATH)
            ]
        );
    }

    /**
     * Get downloadable samples block
     *
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View\Samples
     */
    public function getDownloadableSamplesBlock()
    {
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Catalog\Product\View\Samples',
            [
                'element' => $this->_rootElement->find($this->blockDownloadableSamples, Locator::SELECTOR_XPATH)
            ]
        );
    }

    /**
     * Fill specified option for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        /** @var DownloadableProductInjectable $product */
        $productData = $product->getData();
        $downloadableLinks = isset($productData['downloadable_links']['downloadable']['link'])
            ? $productData['downloadable_links']['downloadable']['link']
            : [];
        $data = $product->getCheckoutData()['options'];

        // Replace link key to label
        foreach ($data['links']  as $key => $linkData) {
            $linkKey = str_replace('link_', '', $linkData['label']);

            $linkData['label'] = isset($downloadableLinks[$linkKey]['title'])
                ? $downloadableLinks[$linkKey]['title']
                : $linkData['label'];

            $data['links'][$key] = $linkData;
        }

        $this->getDownloadableLinksBlock()->fill($data['links']);
        if (isset($data['qty'])) {
            $this->_rootElement->find($this->qty, Locator::SELECTOR_CSS)->setValue($data['qty']);
        }
    }
}
