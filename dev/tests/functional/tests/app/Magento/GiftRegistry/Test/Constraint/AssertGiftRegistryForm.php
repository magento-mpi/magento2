<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryForm
 * Assert that saved GiftRegistry Data matched existed
 */
class AssertGiftRegistryForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = [
        'type_id',
        'event_date'
    ];

    /**
     * Assert that displayed Gift Registry data on edit page equals passed from fixture
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryEdit $giftRegistryEdit
     * @param GiftRegistry $giftRegistry
     * @param GiftRegistry $giftRegistryOrigin [optional]
     * @return void
     */
    public function processAssert(
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryEdit $giftRegistryEdit,
        GiftRegistry $giftRegistry,
        GiftRegistry $giftRegistryOrigin = null
    ) {
        $fixtureData = $giftRegistryOrigin != null
            ? array_merge($giftRegistryOrigin->getData(), $giftRegistry->getData())
            : $giftRegistry->getData();
        $giftRegistryIndex->open();
        $giftRegistryIndex->getGiftRegistryGrid()->eventAction($fixtureData['title'], 'Edit');
        $formData = $giftRegistryEdit->getCustomerEditForm()->getData();
        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry data on edit page equals data from fixture.';
    }
}
