<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Customer;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\PostInstaller;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for customer
     *
     * @var Setup\Customer
     */
    protected $customerSetup;

    /**
     * @var Setup\Review
     */
    protected $reviewSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param Setup\Customer $customerSetup
     * @param Setup\Review $reviewSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        Setup\Customer $customerSetup,
        Setup\Review $reviewSetup,
        PostInstaller $postInstaller
    ) {
        $this->customerSetup = $customerSetup;
        $this->reviewSetup = $reviewSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->customerSetup->run();
        $this->postInstaller->addSetupResource($this->reviewSetup);
    }
}
