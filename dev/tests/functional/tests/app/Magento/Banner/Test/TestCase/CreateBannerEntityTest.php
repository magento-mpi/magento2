<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateBannerEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer segment
 *
 * 2. Steps:
 * 3. Open Backend
 * 4. Go to Content->Banners
 * 5. Click "Add Banner" button
 * 6. Fill data according to dataset
 * 7. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25272
 */
class CreateBannerEntityTest extends Injectable
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
     * Inject pages
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
     * Create banner
     *
     * @param BannerInjectable $banner
     * @return void
     */
    public function test(BannerInjectable $banner)
    {
        $this->bannerIndex->open();
        $this->bannerIndex->getPageActionsBlock()->addNew();
        $this->bannerNew->getNewBannerPageForm()->fill($banner);
        $this->bannerNew->getPageMainActions()->save();
    }
}
