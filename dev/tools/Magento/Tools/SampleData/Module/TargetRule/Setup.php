<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\TargetRule;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for TargetRule module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for products
     *
     * @var Setup\Rule
     */
    protected $ruleSetup;

    /**
     * Constructor
     *
     * @param Setup\Rule $productSetup
     */
    public function __construct(
        Setup\Rule $productSetup
    ) {
        $this->ruleSetup = $productSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->ruleSetup->run();
    }
}
