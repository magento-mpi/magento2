<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;

/**
 * Test Creation for CreateBannerEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer segment
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content->Banners
 * 3. Click "Add Banner" button
 * 4. Fill data according to dataset
 * 5. Perform all assertions
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
        // Steps
        $this->bannerIndex->open();
        $this->bannerIndex->getPageActionsBlock()->addNew();
        $this->bannerNew->getBannerForm()->fill($banner);
        $this->bannerNew->getPageMainActions()->save();
    }
}
