<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Widget;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Widget module
 */
class Setup implements SetupInterface
{
    protected $cmsBlockSetup;

    /**
     * @param Setup\CmsBlock $cmsBlockSetup
     */
    public function __construct(
        Setup\CmsBlock $cmsBlockSetup
    ) {
        $this->cmsBlockSetup = $cmsBlockSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->cmsBlockSetup->run();
    }
}
