<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\SalesRule;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\PostInstaller;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Rule
     */
    protected $ruleSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param Setup\Rule $ruleSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        Setup\Rule $ruleSetup,
        PostInstaller $postInstaller
    ) {
        $this->ruleSetup = $ruleSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->ruleSetup);
    }
}
