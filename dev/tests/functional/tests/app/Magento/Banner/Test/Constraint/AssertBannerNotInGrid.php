<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;

/**
 * Class AssertBannerNotInGrid
 * Assert that deleted banner is absent in grid and can't be found by name
 */
class AssertBannerNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted banner is absent in grid and can't be found by name
     *
     * @param BannerInjectable $banner
     * @param BannerIndex $bannerIndex
     * @return void
     */
    public function processAssert(BannerInjectable $banner, BannerIndex $bannerIndex)
    {
        $bannerIndex->open();
        $filter = [
            'banner' => $banner->getName(),
            'active' => $banner->getIsEnabled(),
        ];

        $storeContent = $banner->getStoreContentsNotUse();
        if (isset($storeContent['value_1']) && $storeContent['value_1'] === 'No') {
            $filter['visibility'] = 'Main Website/Main Website Store/Default Store View';
        }

        $bannerIndex->getGrid()->search($filter);
        if ($banner->hasData('types')) {
            $types = implode(', ', $banner->getTypes());
            $filter['types'] = $types;
        }
        unset($filter['visibility']);

        \PHPUnit_Framework_Assert::assertFalse(
            $bannerIndex->getGrid()->isRowVisible($filter, false),
            'Banner is present in banner grid.'
        );
    }

    /**
     * Banner not in the Banner grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner not in banner grid.';
    }
}
