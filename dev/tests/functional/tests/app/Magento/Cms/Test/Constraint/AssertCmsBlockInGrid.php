<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsBlockInGrid
 * Assert that created CMS block can be found in grid
 */
class AssertCmsBlockInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created CMS block can be found in grid via:
     * title, identifier, store view, status, created and modified date
     *
     * @param CmsBlock $cmsBlock
     * @param CmsBlockIndex $cmsBlockIndex
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(CmsBlock $cmsBlock, CmsBlockIndex $cmsBlockIndex)
    {
        $cmsBlockIndex->open();
        $data = $cmsBlock->getData();
        if (isset($data['stores'])) {
            $storeId = is_array($data['stores']) ? reset($data['stores']) : $data['stores'];
            $parts = explode("/", $storeId);
        }

        $filter = [
            'title' => $data['title'],
            'identifier' => $data['identifier'],
            'is_active' => $data['is_active'],
            'store_id' => end($parts),
        ];

        // add creation_time & update_time to filter if there are ones
        if (isset($data['creation_time'])) {
            $filter['creation_time_from'] = date("M j, Y", strtotime($cmsBlock->getCreationTime()));
        }
        if (isset($data['update_time'])) {
            $filter['update_time_from'] = date("M j, Y", strtotime($cmsBlock->getUpdateTime()));
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $cmsBlockIndex->getCmsBlockGrid()->isRowVisible($filter, true, false),
            'CMS Block with '
            . 'title \'' . $filter['title'] . '\', '
            . 'identifier \'' . $filter['identifier'] . '\', '
            . 'store view \'' . $filter['store_id'] . '\', '
            . 'status \'' . $filter['is_active'] . '\', '
            . (isset($filter['creation_time_from'])
                ? ('creation_time \'' . $filter['creation_time_from'] . '\', ')
                : '')
            . (isset($filter['update_time_from']) ? ('update_time \'' . $filter['update_time_from'] . '\'') : '')
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
        return 'CMS Block is present in grid.';
    }
}
