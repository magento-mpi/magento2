<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\GiftRegistry;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for GiftRegistry module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\GiftRegistry
     */
    protected $giftRegistrySetup;

    /**
     * @param Setup\GiftRegistry $giftRegistrySetup
     */
    public function __construct(
        Setup\GiftRegistry $giftRegistrySetup
    ) {
        $this->giftRegistrySetup = $giftRegistrySetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->giftRegistrySetup->run();
    }
}
