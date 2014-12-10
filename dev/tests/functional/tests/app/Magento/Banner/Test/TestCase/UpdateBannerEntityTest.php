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
 * Test creation for UpdateBannerEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Banner is created
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content->Banners
 * 3. Open created banner
 * 4. Fill data according to dataSet
 * 5. Save banner
 * 6. Perform all assertions
 *
 * @group Banner_(PS)
 * @ZephyrId MAGETWO-25639
 */
class UpdateBannerEntityTest extends Injectable
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
     * Update banner entity
     *
     * @param BannerInjectable $bannerOrigin
     * @param BannerInjectable $banner
     * @return void
     */
    public function test(BannerInjectable $bannerOrigin, BannerInjectable $banner)
    {
        // Precondition
        $bannerOrigin->persist();
        $filter = ['banner' => $bannerOrigin->getName()];
        // Steps
        $this->bannerIndex->open();
        $this->bannerIndex->getGrid()->searchAndOpen($filter);
        $this->bannerNew->getBannerForm()->fill($banner);
        $this->bannerNew->getPageMainActions()->save();
    }
}
