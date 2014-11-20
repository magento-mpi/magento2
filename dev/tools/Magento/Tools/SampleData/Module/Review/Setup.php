<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Review;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for Review module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Review
     */
    protected $reviewSetup;

    /**
     * @param Setup\Review $reviewSetup
     */
    public function __construct(
        Setup\Review $reviewSetup
    ) {
        $this->reviewSetup = $reviewSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->reviewSetup->run();
    }
}
