<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;

/**
 * Class AssertCmsPageInitialVersionInGrid
 * Assert that initial CMS page version can be found on CMS page Versions tab in grid
 */
class AssertCmsPageInitialVersionInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * CmsNew Page
     *
     * @var CmsNew
     */
    protected $cmsNew;

    /**
     * CmsIndex Page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Search version by filter and perform assert
     *
     * @param CmsPage $cms
     * @param array $results
     * @return void
     */
    protected function searchVersion(CmsPage $cms, array $results)
    {
        if (!isset($results['quantity'])) {
            preg_match('/\d+/', $results['revision'], $matches);
            $quantity = $matches[0];
        } else {
            $quantity = $results['quantity'];
        }
        $filter = [
            'label' => isset($results['label']) ? $results['label'] : $cms->getTitle(),
            'owner' => $results['owner'],
            'access_level' => $results['access_level'],
            'quantity' => $quantity,
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $this->cmsNew->getPageVersionsForm()->getTabElement('versions')->getVersionsGrid()->isRowVisible($filter),
            'CMS Page Version with '
            . 'label \'' . $filter['label'] . '\', '
            . 'owner \'' . $filter['owner'] . '\', '
            . 'access level \'' . $filter['access_level'] . '\', '
            . 'quantity \'' . $filter['quantity'] . '\', '
            . 'is absent in CMS Page Versions grid.'
        );
    }

    /**
     * Assert that initial CMS page version can be found on CMS page Versions tab in grid via:
     * Version label, Owner, Quantity, Access Level
     *
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @param CmsPage $cmsInitial[optional]
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsNew $cmsNew,
        CmsIndex $cmsIndex,
        array $results,
        CmsPage $cmsInitial = null
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageVersionsForm()->openTab('versions');
        $this->searchVersion($cmsInitial, $results);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Initial Version is present in grid.';
    }
}
