<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Fixture\GiftRegistryType;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryNew;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertGiftRegistryTypeForm
 * Assert that GiftRegistryType form filled correctly
 */
class AssertGiftRegistryTypeForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that GiftRegistryType form filled correctly
     *
     * @param GiftRegistryType $giftRegistryType
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryNew $giftRegistryNew
     * @return void
     */
    public function processAssert(
        GiftRegistryType $giftRegistryType,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryNew $giftRegistryNew
    ) {
        $filter = ['label' => $giftRegistryType->getLabel()];
        $giftRegistryIndex->getGiftRegistryGrid()->searchAndOpen($filter);
        $formData = $giftRegistryNew->getGiftRegistryForm()->getData($giftRegistryType);
        $fixtureData = $giftRegistryType->getData();
        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
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
