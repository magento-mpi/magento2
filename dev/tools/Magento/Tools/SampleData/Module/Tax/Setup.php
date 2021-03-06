<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\Tax;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for Tax module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Tax
     */
    protected $taxSetup;

    /**
     * @param Setup\Tax $taxSetup
     */
    public function __construct(
        Setup\Tax $taxSetup
    ) {
        $this->taxSetup = $taxSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->taxSetup->run();
    }
}
