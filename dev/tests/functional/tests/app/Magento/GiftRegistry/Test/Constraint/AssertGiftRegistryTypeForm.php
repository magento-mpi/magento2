<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryNew;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertGiftRegistryTypeForm
 * Assert that GiftRegistryType form filled correctly
 */
class AssertGiftRegistryTypeForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that GiftRegistryType form filled correctly
     *
     * @param GiftRegistry $giftRegistry
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryNew $giftRegistryNew
     * @return void
     */
    public function processAssert(
        GiftRegistry $giftRegistry,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryNew $giftRegistryNew
    ) {
        $filter = ['label' => $giftRegistry->getLabel()];
        $giftRegistryIndex->getGiftRegistryGrid()->searchAndOpen($filter);
        $formData = $giftRegistryNew->getGiftRegistryForm()->getData($giftRegistry);
        $fixtureData = $giftRegistry->getData();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureData,
            $formData,
            'Form data is not equal to fixture data.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data is equal to fixture data.';
    }
}
