<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\TEst\Fixture\GiftRegistryType;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryTypeNotInGrid
 * Assert that deleted Gift Registry type is absent in Stores > Gift Registry grid in backend
 */
class AssertGiftRegistryTypeNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted Gift Registry type is absent in Stores > Gift Registry grid in backend
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryType $giftRegistryType
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex, GiftRegistryType $giftRegistryType)
    {
        $giftRegistryIndex->open();
        $filter = ['label' => $giftRegistryType->getLabel()];
        \PHPUnit_Framework_Assert::assertFalse(
            $giftRegistryIndex->getGiftRegistryGrid()->isRowVisible($filter),
            'Gift registry \'' . $giftRegistryType->getLabel() . '\' is present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry type absent in GiftRegistryType grid.';
    }
}
