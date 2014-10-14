<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Adminhtml\Product\Composite;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Configure
 * Adminhtml downloadable product composite configure block
 */
class Configure extends \Magento\Catalog\Test\Block\Adminhtml\Product\Composite\Configure
{
    /**
     * Fill options for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $data = $this->dataMapping($product->getData());
        $this->_fill($data);
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $productOptions = [];
        $checkoutData = $fields['checkout_data']['options'];
        $productLinks = $fields['downloadable_links']['downloadable']['link'];

        if (!empty($checkoutData['links'])) {
            $linkMapping = parent::dataMapping(['link' => '']);
            $selector = $linkMapping['link']['selector'];
            foreach ($checkoutData['links'] as $key => $link) {
                $link['label'] = $productLinks[str_replace('link_', '', $link['label'])]['title'];
                $linkMapping['link']['selector'] = str_replace('%link_name%', $link['label'], $selector);
                $linkMapping['link']['value'] = $link['value'];
                $productOptions['link_' . $key] = $linkMapping['link'];
            }
        }

        return $productOptions;
    }
}
