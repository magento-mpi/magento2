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
use Magento\GiftRegistry\Test\Fixture\GiftRegistryPerson;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Class AssertGiftRegistryForm
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
     * @param GiftRegistryPerson $giftRegistryPerson
     * @param AddressInjectable $address
     * @return void
     */
    public function processAssert(
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryEdit $giftRegistryEdit,
        GiftRegistry $giftRegistry,
        GiftRegistryPerson $giftRegistryPerson,
        AddressInjectable $address
    ) {
        $giftRegistryIndex->open();
        $giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Edit');
        $generalInformationFormData = $giftRegistryEdit->getGeneralInformationForm()->getData();
        $eventInformationFormData = $giftRegistryEdit->getEventInformationForm()->getData();
        $recipientsInformationFormData = $giftRegistryEdit->getRecipientsInformationForm()->getData();
        $shippingAddressFormData = $giftRegistryEdit->getShippingAddressForm()->getData();
        $giftRegistryPropertiesFormData = ($giftRegistry->getTypeId() == 'Baby Registry')
            ? $giftRegistryEdit->getGiftRegistryPropertiesForm()->getData()
            : [];
        $formData = array_merge(
            $generalInformationFormData,
            $eventInformationFormData,
            $recipientsInformationFormData,
            $shippingAddressFormData,
            $giftRegistryPropertiesFormData
        );
        $fixtureData = array_merge($giftRegistry->getData(), $giftRegistryPerson->getData(), $address->getData());
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
