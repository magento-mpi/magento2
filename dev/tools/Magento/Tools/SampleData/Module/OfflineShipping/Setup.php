<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\OfflineShipping;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for OfflineShipping module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Tablerate
     */
    protected $tablerateSetup;

    /**
     * @param Setup\Tablerate $tablerateSetup
     */
    public function __construct(
        Setup\Tablerate $tablerateSetup
    ) {
        $this->tablerateSetup = $tablerateSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->tablerateSetup->run();
    }
}
