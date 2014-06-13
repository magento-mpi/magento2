<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;

/**
 * Class AssertCmsPageForm
 * Assert that displayed CMS page data on edit page equals passed from fixture
 */
class AssertCmsPageForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed CMS page data on edit page equals passed from fixture
     *
     * @param CmsPage $cms
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @return void
     */
    public function processAssert(CmsPage $cms, CmsIndex $cmsIndex, CmsNew $cmsNew)
    {
        $cmsIndex->open();
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->getCmsPageGridBlock()->searchAndSelect($filter);

        $cmsFormData = $cmsNew->getNewCmsPageForm()->getData($cms);
        $cmsFixtureData = $cms->getData();
        $diff = array_diff($cmsFixtureData, $cmsFormData);

        \PHPUnit_Framework_Assert::assertTrue(
            empty($diff),
            'CMS Page data not equals to passed from fixture.'
        );
    }

    /**
     * CMS page data on edit page equals data from fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS page data on edit page equals data from fixture.';
    }
}
