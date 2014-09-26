<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\RecurringPayment;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for RecurringPayment module
 * @package Magento\Tools\SampleData\Module\RecurringPayment
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for products
     *
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * Constructor
     *
     * @param Setup\Product $productSetup

     */
    public function __construct(
        Setup\Product $productSetup
    ) {
        $this->productSetup = $productSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->productSetup->run();
    }
}
