<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockNew;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;

/**
 * Test creation for DeleteCmsBlockEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS Block
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content -> Blocks
 * 3. Open created CMS block
 * 4. Click "Delete Block"
 * 5. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25698
 */
class DeleteCmsBlockEntityTest extends Injectable
{
    /**
     * Page CmsBlockIndex
     *
     * @var CmsBlockIndex
     */
    protected $cmsBlockIndex;

    /**
     * Page CmsBlockNew
     *
     * @var CmsBlockNew
     */
    protected $cmsBlockNew;

    /**
     * Injection data
     *
     * @param CmsBlockIndex $cmsBlockIndex
     * @param CmsBlockNew $cmsBlockNew
     * @return void
     */
    public function __inject(
        CmsBlockIndex $cmsBlockIndex,
        CmsBlockNew $cmsBlockNew
    ) {
        $this->cmsBlockIndex = $cmsBlockIndex;
        $this->cmsBlockNew = $cmsBlockNew;
    }

    /**
     * Delete CMS Block
     *
     * @param CmsBlock $cmsBlock
     * @return void
     */
    public function test(CmsBlock $cmsBlock)
    {
        $this->markTestIncomplete('MAGETWO-28916');
        // Precondition
        $cmsBlock->persist();

        // Steps
        $filter = [
            'identifier' => $cmsBlock->getIdentifier(),
        ];
        $this->cmsBlockIndex->open();
        $this->cmsBlockIndex->getCmsBlockGrid()->searchAndOpen($filter);
        $this->cmsBlockNew->getFormPageActions()->delete();
    }
}
