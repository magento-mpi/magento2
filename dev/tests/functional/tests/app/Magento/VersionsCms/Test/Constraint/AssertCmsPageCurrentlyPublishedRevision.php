<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\VersionsCms\Test\Fixture\Revision;

/**
 * Class AssertCmsPageCurrentlyPublishedRevision
 * Assert that link to Currently Published Revision on CMS Page Information Form is available
 */
class AssertCmsPageCurrentlyPublishedRevision extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that link to Currently Published Revision on CMS Page Information Form is available
     *
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @param Revision|null $revision [optional]
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsNew $cmsNew,
        CmsIndex $cmsIndex,
        array $results,
        Revision $revision = null
    ) {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $formPublishedRevision = $cmsNew->getPageVersionsForm()->getCurrentlyPublishedRevisionText();
        $fixturePublishedRevision = $cms->getTitle() . '; ' . $results['revision'];
        \PHPUnit_Framework_Assert::assertEquals(
            $fixturePublishedRevision,
            $formPublishedRevision,
            'Link to Currently Published Revision not equals to passed in fixture.'
        );
        $cmsNew->getPageForm()->openTab('content');
        $formRevisionData = $cmsNew->getPageVersionsForm()->getTabElement('revision_content')->getContentData();
        preg_match('/\d+/', $results['revision'], $matches);
        $fixtureRevisionData['revision'] = $matches[0];
        $fixtureRevisionData['version'] = $cms->getTitle();
        $fixtureRevisionData['content'] = $revision !== null
            ? ['content' => $revision->getContent()]
            : $cms->getContent();
        $error = $this->verifyData($fixtureRevisionData, $formRevisionData);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Link to Currently Published Revision on CMS Page Information Form is available.';
    }
}
