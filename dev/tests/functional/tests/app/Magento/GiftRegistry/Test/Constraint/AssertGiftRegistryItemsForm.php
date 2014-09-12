<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\GiftRegistry\Test\Page\GiftRegistryItems;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;

/**
 * Class AssertGiftRegistryItemsForm
 * Assert that updated GiftRegistry items data matched existed
 */
class AssertGiftRegistryItemsForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed Gift Registry items data on edit page equals passed from fixture
     *
     * @param GiftRegistryItems $giftRegistryItems
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param array $updateOptions
     * @return void
     */
    public function processAssert
    (
        GiftRegistryItems $giftRegistryItems,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry,
        GiftRegistryIndex $giftRegistryIndex,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        $updateOptions
    ) {
        $cmsIndex->getLinksBlock()->openLink("My Account");
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Registry');
        $giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Manage Items');
        $formData = $giftRegistryItems->getGiftRegistryItemsBlock()->getItemData($product);
        $errors = $this->verifyData($updateOptions, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry items data on edit page equals data from fixture.';
    }
}
