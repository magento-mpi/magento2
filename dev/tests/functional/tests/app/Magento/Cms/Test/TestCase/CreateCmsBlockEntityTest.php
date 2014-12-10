<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsBlock;

/**
 * Test Coverage for CreateCmsBlockEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create store view
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content->Blocks
 * 3. Click "Add New Block" button
 * 4. Fill data according to dataset
 * 5. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25578
 */
class CreateCmsBlockEntityTest extends AbstractCmsBlockEntityTest
{
    /**
     * Create CMS Block
     *
     * @param CmsBlock $cmsBlock
     * @return void
     */
    public function test(CmsBlock $cmsBlock)
    {
        $this->markTestIncomplete('MAGETWO-28916, MAGETWO-30362');
        // Prepare data for tearDown
        $this->storeName = $cmsBlock->getStores();

        // Steps
        $this->cmsBlockIndex->open();
        $this->cmsBlockIndex->getGridPageActions()->addNew();
        $this->cmsBlockNew->getCmsForm()->fill($cmsBlock);
        $this->cmsBlockNew->getFormPageActions()->save();
    }
}
