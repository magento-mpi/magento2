<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Downloadable\Test\Fixture\Cart;

use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Item
 * Data for verify cart item block on checkout page
 *
 * Data keys:
 *  - product (fixture data for verify)
 */
class Item extends \Magento\Catalog\Test\Fixture\Cart\Item
{
    /**
     * @constructor
     * @param FixtureInterface $product
     */
    public function __construct(FixtureInterface $product)
    {
        parent::__construct($product);

        /** @var DownloadableProductInjectable $product */
        $checkoutDownloadableOptions = [];
        $checkoutData = $product->getCheckoutData();
        $downloadableOptions = $product->getDownloadableLinks();
        foreach ($checkoutData['options']['links'] as $link) {
            $keyLink = str_replace('link_', '', $link['label']);
            $checkoutDownloadableOptions[] = [
                'title' => 'Links',
                'value' => $downloadableOptions['downloadable']['link'][$keyLink]['title'],
            ];
        }

        $this->data['options'] += $checkoutDownloadableOptions;
    }
}
