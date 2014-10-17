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
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * Constructor
     *
     * @param Setup\Block $blockSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        Setup\Block $blockSetup,
        PostInstaller $postInstaller
    ) {
        $this->blockSetup = $blockSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->blockSetup);
    }
}
