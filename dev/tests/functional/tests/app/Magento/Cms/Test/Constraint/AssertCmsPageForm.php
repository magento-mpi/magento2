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
     * @param CmsPage $cmsOriginal
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsIndex $cmsIndex,
        CmsNew $cmsNew,
        CmsPage $cmsOriginal = null
    ) {
        $cmsIndex->open();
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);

        $cmsFormData = $cmsNew->getPageForm()->getData();
        $fixtureData = $cmsOriginal !== null
            ? array_merge($cmsOriginal->getData(), $cms->getData())
            : $cms->getData();
        $dataDiff = $this->verifyForm($cmsFormData, $fixtureData);

        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'CMS Page data not equals to passed from fixture.'
            . "\nLog:\n" . implode(";\n", $dataDiff)
        );
    }

    /**
     * Verifying that form is filled right
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array $errorMessage
     */
    protected function verifyForm(array $formData, array $fixtureData)
    {
        $errorMessage = [];
        $formData['store_id'] = implode('/', $formData['store_id']);
        foreach ($fixtureData as $key => $value) {
            if ($key == 'page_id') {
                continue;
            }
            if ($value !== $formData[$key] && !in_array($key, $this->skippedFields)) {
                $errorMessage[] = "Data in " . $key . " field not equal."
                    . "\nExpected: " . $value
                    . "\nActual: " . $formData[$key];
            }
        }

        return $errorMessage;
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
