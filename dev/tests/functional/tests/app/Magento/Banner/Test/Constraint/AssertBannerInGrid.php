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
 * Class AssertBannerInGrid
 * Assert that created banner is found by name and has correct banner types, visibility, status
 */
class AssertBannerInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created banner is found by name and has correct banner types, visibility, status
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
        if ($banner->getStoreContentsNotUse1() == 'No') {
            $filter['visibility'] = 'Main Website/Main Website Store/Default Store View';
        }
        $types = '';
        if ($banner->hasData('types')) {
            $types = implode(', ', $banner->getTypes());
        }
        $isBanner = $bannerIndex->getGrid()->isBannerRowVisible($filter, $types);
        \PHPUnit_Framework_Assert::assertTrue(
            $isBanner,
            'Banner is absent in banner grid.'
        );
    }

    /**
     * Text present Banner in the Banner grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner in grid.';
    }
}
