<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Handler;

use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new gift card product via curl
 */
class Curl extends AbstractCurl implements GiftCardProductInterface
{
    /**
     * Persist fixture
     *
     * @param FixtureInterface $fixture
     * @return mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->extendPlaceholder();
        return parent::persist($fixture);
    }

    /**
     * Expand basic placeholder
     *
     * @return void
     */
    protected function extendPlaceholder()
    {
        $this->placeholderData += [
            'giftcard_type' => [
                'Virtual' => 0,
                'Physical' => 1,
                'Combined' => 2
            ],
            'website_id' => [
                'All Websites [USD]' => 0
            ],
            'delete' => [
                'Yes' => 1,
                'No' => 0
            ],
            'allow_open_amount' => [
                'Yes' => 1,
                'No' => 0
            ],
            'use_config_allow_message' => [
                'Yes' => 1,
                'No' => 0
            ],
            'use_config_is_redeemable' => [
                'Yes' => 1,
                'No' => 0
            ],
            'use_config_lifetime' => [
                'Yes' => 1,
                'No' => 0
            ],
            'use_config_email_template' => [
                'Yes' => 1,
                'No' => 0
            ],
            'allow_message' => [
                'Yes' => 1,
                'No' => 0
            ],
            'email_template' => [
                'Gift Card(s) Purchase (Default)' => 'giftcard_email_template'
            ]
        ];
    }
}
