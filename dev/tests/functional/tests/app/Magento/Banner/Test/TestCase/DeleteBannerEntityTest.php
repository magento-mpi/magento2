<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\TestCase;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Mtf\TestCase\Injectable;

/**
 * Test creation for Delete BannerEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create banner
 *
 * 1. Steps:
 * 2. Open Backend
 * 3. Go to Content->Banners
 * 4. Open created banner
 * 5. Click "Delete Banner"
 * 6. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25644
 */
class DeleteBannerEntityTest extends Injectable
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
        $filter = ['banner' => $banner->getName()];
        // Steps
        $this->bannerIndex->open();
        $this->bannerIndex->getGrid()->searchAndOpen($filter);
        $this->bannerNew->getPageMainActions()->delete();
    }
}
