<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\Handler\GiftCardProduct;

use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;
use Mtf\Fixture\FixtureInterface;
use Mtf\System\Config;

/**
 * Class Curl
 * Create new gift card product via curl
 */
class Curl extends ProductCurl implements GiftCardProductInterface
{
    /**
     * Constructor
     *
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        parent::__construct($configuration);

        $this->mappingData += [
            'giftcard_type' => [
                'Virtual' => 0,
                'Physical' => 1,
                'Combined' => 2,
            ],
            'website_id' => [
                'All Websites [USD]' => 0,
            ],
            'delete' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'allow_open_amount' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'use_config_allow_message' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'use_config_is_redeemable' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'use_config_lifetime' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'use_config_email_template' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'allow_message' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'email_template' => [
                'Gift Card(s) Purchase (Default)' => 'giftcard_email_template',
            ]
        ];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $data = parent::prepareData($fixture, $prefix);
        if (isset($data[$prefix]['giftcard_amounts'])) {
            foreach ($data[$prefix]['giftcard_amounts'] as $key => $amounts) {
                if (!isset($amounts['website_id'])) {
                    $data[$prefix]['giftcard_amounts'][$key]['website_id'] = 0;
                }
            }
        }

        return $data;
    }
}
