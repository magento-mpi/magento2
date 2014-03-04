<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage as CmsPageFixture;
use Magento\Cms\Test\Page\AdminHtml\CmsPageGrid;
use Magento\Cms\Test\Page\CmsPage;

/**
 * Class CreatePageTest
 *
 * @package Magento\Cms\Test\TestCase
 */
class CreatePageTest extends Injectable
{
    /**
     * @var CmsPageGrid
     */
    protected $cmsPageGrid;

    /**
     * @var CmsPage
     */
    protected $cmsPage;

    /**
     * Login to backend as a precondition to test
     *
     * @return void
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * @param CmsPageGrid $cmsPageGrid
     * @param CmsPage $cmsPage
     */
    public function __inject(CmsPageGrid $cmsPageGrid, CmsPage $cmsPage)
    {
        $this->cmsPageGrid = $cmsPageGrid;
        $this->cmsPage = $cmsPage;
    }

    /**
     * Creating CMS content page
     *
     * @param CmsPageFixture $cmsPageFixture
     * @ZephyrId MAGETWO-12399
     */
    public function testCreateCmsPage(CmsPageFixture $cmsPageFixture)
    {
        $this->cmsPageGrid->open();
        $cmsPageGridBlock = $this->cmsPageGrid->getCmsPageGridBlock();
        $cmsPageGridBlock->addNewCmsPage();
        $cmsPageNew = Factory::getPageFactory()->getAdminCmsPageNew();
        $cmsPageNewForm = $cmsPageNew->getNewCmsPageForm();
        $cmsPageNewForm->fill($cmsPageFixture);
        $cmsPageNewForm->save();
    }
}
