<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsBlock;

/**
 * Test Creation for UpdateCmsBlockEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create store view
 * 2. Create CMS Block
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content -> Blocks
 * 3. Open created CMS block
 * 4. Fill data according to dataset
 * 5. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25941
 */
class UpdateCmsBlockEntityTest extends CmsBlockEntityTest
{
    /**
     * Run Update CMS Block test
     *
     * @param CmsBlock $initialCmsBlock
     * @param CmsBlock $cmsBlock
     * @return void
     */
    public function test(CmsBlock $initialCmsBlock, CmsBlock $cmsBlock)
    {
        // Precondition
        $initialCmsBlock->persist();

        // Prepare data for tearDownAfterClass
        self::$storeName = $cmsBlock->getStoreId();

        // Steps
        $filter = [
            'identifier' => $initialCmsBlock->getIdentifier(),
        ];
        $this->cmsBlockIndex->open();

        /**
         * TODO: MAGETWO-25640
         * Search doesn't work for CMS Blocks grid bug
         */
        // $this->cmsBlockIndex->getCmsBlockGrid()->searchAndOpen($filter);
        // $this->cmsBlockNew->getCmsForm()->fill($cmsBlock);
        // $this->cmsBlockNew->getFormPageActions()->save();

        $this->cmsBlockIndex->getCmsBlockGrid()->sortAndOpen();
        $this->cmsBlockNew->getCmsForm()->fill($cmsBlock);
        $this->cmsBlockNew->getFormPageActions()->save();
    }
}
