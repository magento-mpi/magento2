<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Catalog\Product;

use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product\View as ParentView;

/**
 * Class View
 * Downloadable product view block on the product page
 */
class View extends ParentView
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
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View\DownloadableLinks
     */
    public function getDownloadableLinksBlock()
    {
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Catalog\Product\View\DownloadableLinks',
            [
                'element' => $this->_rootElement->find($this->blockDownloadableLinks, Locator::SELECTOR_XPATH)
            ]
        );
    }

    /**
     * Get downloadable samples block
     *
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View\DownloadableSamples
     */
    public function getDownloadableSamplesBlock()
    {
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Catalog\Product\View\DownloadableSamples',
            [
                'element' => $this->_rootElement->find($this->blockDownloadableSamples, Locator::SELECTOR_XPATH)
            ]
        );
    }
}
