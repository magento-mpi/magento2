<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\GiftCard;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for GiftCard module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Product
     */
    protected $productSetup;

    /**
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
