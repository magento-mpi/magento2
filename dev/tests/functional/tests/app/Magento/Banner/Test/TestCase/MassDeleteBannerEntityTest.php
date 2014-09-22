<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for MassDeleteBannerEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create banner
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content->Banners
 * 3. Find created banner in grid
 * 4. Mark mass action checkbox
 * 5. Select "Delete" in mass action dropdown and click "Submit" button
 * 6. Perform all assertions
 *
 * @group Banner_(PS)
 * @ZephyrId MAGETWO-26677
 */
class MassDeleteBannerEntityTest extends Injectable
{
    /**
     * BannerIndex page
     *
     * @var BannerIndex
     */
    protected $bannerIndex;

    /**
     * BannerNew page
     *
     * @var BannerNew
     */
    protected $bannerNew;

    /**
     * Inject data
     *
     * @param BannerIndex $bannerIndex
     * @param BannerNew $bannerNew
     * @return void
     */
    public function __inject(BannerIndex $bannerIndex, BannerNew $bannerNew)
    {
        $this->bannerIndex = $bannerIndex;
        $this->bannerNew = $bannerNew;
    }

    /**
     * Delete banner entity
     *
     * @param BannerInjectable $banner
     * @return void
     */
    public function test(BannerInjectable $banner)
    {
        // Precondition
        $banner->persist();
        // Steps
        $deleteBanner[] = ['banner' => $banner->getName()];
        $this->bannerIndex->open();
        $this->bannerIndex->getGrid()->massaction($deleteBanner, 'Delete', true);
    }
}
