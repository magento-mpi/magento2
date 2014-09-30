<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateGiftCardAccountStep
 * Creating gift card account
 */
class CreateGiftCardAccountStep implements TestStepInterface
{
    /**
     * Gift card account name in data set
     *
     * @var string
     */
    protected $giftCardAccount;

    /**
     * Factory for Fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $giftCardAccount
     */
    public function __construct(FixtureFactory $fixtureFactory, $giftCardAccount)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->giftCardAccount = $giftCardAccount;
    }

    /**
     * Creating sales rule
     *
     * @return array
     */
    public function run()
    {
        $result['giftCardAccount'] = null;
        if ($this->giftCardAccount != '-') {
            $giftCardAccount = $this->fixtureFactory->createByCode(
                'giftCardAccount',
                ['dataSet' => $this->giftCardAccount]
            );
            $giftCardAccount->persist();
            $result['giftCardAccount'] = $giftCardAccount;
        }

        return $result;
    }
}
