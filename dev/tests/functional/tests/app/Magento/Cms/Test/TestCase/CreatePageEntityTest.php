<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage as CmsPageFixture;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\CmsPage;

/**
 * Class CreatePageTest
 */
class CreatePageEntityTest extends Injectable
{
    /**
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * @var CmsNew
     */
    protected $cmsNew;

    /**
     * @var CmsPage
     */
    protected $cmsPage;

    /**
     * @param CmsIndex $cmsIndex
     * @param CmsPage $cmsPage
     * @param CmsNew $cmsNew
     * @return void
     */
    public function __inject(CmsIndex $cmsIndex, CmsPage $cmsPage, CmsNew $cmsNew)
    {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        $this->cmsPage = $cmsPage;
    }

    /**
     * Creating CMS content page
     *
     * @param CmsPageFixture $cms
     * @return void
     * @ZephyrId MAGETWO-12399
     */
    public function test(CmsPageFixture $cms)
    {
        $this->cmsIndex->open();
        $cmsPageGridBlock = $this->cmsIndex->getPageActionsBlock();
        $cmsPageGridBlock->addNew();
        $this->cmsNew->getPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
