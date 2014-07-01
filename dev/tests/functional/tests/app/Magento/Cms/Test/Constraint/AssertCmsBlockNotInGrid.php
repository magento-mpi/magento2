<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;

/**
 * Class AssertCmsBlockNotInGrid
 * Assert that created CMS block can't be found in grid via:
 * title, identifier, store view, status, created and modified date
 */
class AssertCmsBlockNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS block can't be found in grid via:
     * title, identifier, store view, status, created and modified date
     *
     * @param CmsBlock $cmsBlock
     * @param CmsBlockIndex $cmsBlockIndex
     * @return void
     */
    public function processAssert(CmsBlock $cmsBlock, CmsBlockIndex $cmsBlockIndex)
    {
        $data = $cmsBlock->getData();
        $storeId = is_array($data['store_id']) ? reset($data['store_id']) : $data['store_id'];
        $parts = explode("/", $storeId);

        $filter = [
            'title' => $data['title'],
            'identifier' => $data['identifier'],
            'is_active' => $data['is_active'],
            'store_id' => end($parts),
        ];

        // add creation_time & update_time to filter if there are ones
        if (isset($data['creation_time'])) {
            $filter['creation_time'] = date("M j, Y", strtotime($cmsBlock->getCreationTime()));
        }
        if (isset($data['update_time'])) {
            $filter['update_time'] = date("M j, Y", strtotime($cmsBlock->getUpdateTime()));
        }

        \PHPUnit_Framework_Assert::assertFalse(
            $cmsBlockIndex->getCmsBlockGrid()->isRowVisible($filter, true, false),
            'CMS Block with '
            . 'title \'' . $filter['title'] . '\', '
            . 'identifier \'' . $filter['identifier'] . '\', '
            . 'store view \'' . $filter['store_id'] . '\', '
            . 'status \'' . $filter['is_active'] . '\', '
            . (isset($filter['creation_time']) ? ('creation_time \'' . $filter['creation_time'] . '\', ') : '')
            . (isset($filter['update_time']) ? ('update_time \'' . $filter['update_time'] . '\', ') : '')
            . 'is absent in CMS Block grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Block is not present in grid.';
    }
}
