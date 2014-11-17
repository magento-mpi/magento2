<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Cms;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\PostInstaller;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for blocks
     *
     * @var Setup\Block
     */
    protected $blockSetup;

    /**
     * Setup class for CMS Page
     *
     * @var Setup\Page
     */
    protected $pageSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param Setup\Block $blockSetup
     * @param Setup\Page $pageSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        Setup\Block $blockSetup,
        Setup\Page $pageSetup,
        PostInstaller $postInstaller
    ) {
        $this->blockSetup = $blockSetup;
        $this->pageSetup = $pageSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->pageSetup->run();
        $this->postInstaller->addSetupResource($this->blockSetup);
    }
}
