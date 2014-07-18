<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase\OnePageCheckoutTest\Test;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class CreateGiftCardAccount
 * Creating gift card account
 */
class CreateGiftCardAccount implements StepInterface
{
    /**
     * Gift card account name in data set
     *
     * @var string
     */
    protected $giftCardAccount;

    /**
     * Sales rule name in data set
     *
     * @var string
     */
    protected $salesRule;

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
     * @param string $salesRule
     */
    public function __construct(FixtureFactory $fixtureFactory, $giftCardAccount, $salesRule)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->giftCardAccount = $giftCardAccount;
        $this->salesRule = $salesRule;
    }

    /**
     * Run step that creating gift card account or sales rule
     *
     * @return array
     */
    public function run()
    {
        $result = [];
        if ($this->giftCardAccount != '-') {
            $giftCardAccount = $this->fixtureFactory->createByCode(
                'giftCardAccount',
                ['dataSet' => $this->giftCardAccount]
            );
            $giftCardAccount->persist();
            $result['giftCardAccount'] = $giftCardAccount;
        }
        if ($this->salesRule != '-') {
            $salesRule = $this->fixtureFactory->createByCode(
                'salesRuleInjectable',
                ['dataSet' => $this->salesRule]
            );
            $salesRule->persist();
            $result['salesRule'] = $salesRule;
        }
        return $result;
    }
}
