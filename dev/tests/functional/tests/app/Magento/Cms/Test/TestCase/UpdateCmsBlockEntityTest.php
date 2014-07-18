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
class UpdateCmsBlockEntityTest extends AbstractCmsBlockEntityTest
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

        // Prepare data for tearDown
        $this->storeName = $cmsBlock->getStores();

        // Steps
        $filter = [
            'identifier' => $initialCmsBlock->getIdentifier(),
        ];
        $this->cmsBlockIndex->open();
        $this->cmsBlockIndex->getCmsBlockGrid()->searchAndOpen($filter);
        $this->cmsBlockNew->getCmsForm()->fill($cmsBlock);
        $this->cmsBlockNew->getFormPageActions()->save();
    }
}
