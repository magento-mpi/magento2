<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;

/**
 * Class AssertCmsPageForm
 * Assert that displayed CMS page data on edit page equals passed from fixture
 */
class AssertCmsPageForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = [
        'page_id',
        'content',
        'content_heading',
        'custom_theme_from',
        'custom_theme_to'
    ];

    /**
     * Assert that displayed CMS page data on edit page equals passed from fixture
     *
     * @param CmsPage $cms
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsIndex $cmsIndex,
        CmsNew $cmsNew
    ) {
        $cmsIndex->open();
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);

        $cmsFormData = $cmsNew->getPageForm()->getData($cms);
        $cmsFormData['store_id'] = implode('/', $cmsFormData['store_id']);
        $errors = $this->verifyData($cms->getData(), $cmsFormData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
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
