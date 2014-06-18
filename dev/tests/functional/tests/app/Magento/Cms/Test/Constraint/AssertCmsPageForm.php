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
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);

        $cmsFormData = $cmsNew->getNewCmsPageForm()->getData($cms);
        $cmsFixtureData = $cms->getData();
        $dataDiff = $this->verifyForm($cmsFormData, $cmsFixtureData);

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

        foreach ($fixtureData as $key => $value) {
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                $diff = array_merge($diff, array_diff($formData[$key], $value));
                if (!empty($diff)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . implode(", ", $value)
                        . "\nActual: " . implode(", ", $formData[$key]);
                }
            } else {
                if ($value !== $formData[$key]) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . $value
                        . "\nActual: " . $formData[$key];
                }
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
