<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\CatalogRule;

use Magento\Tools\SampleData\Helper\PostInstaller;
use Magento\Tools\SampleData\SetupInterface;

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
