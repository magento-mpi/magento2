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
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @param Setup\Customer $customerSetup
     * @param Setup\Review $reviewSetup
     * @param PostInstaller $postInstaller
     * @param \Magento\Framework\Module\ModuleListInterface
     */
    public function __construct(
        Setup\Customer $customerSetup,
        Setup\Review $reviewSetup,
        PostInstaller $postInstaller,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->customerSetup = $customerSetup;
        $this->reviewSetup = $reviewSetup;
        $this->postInstaller = $postInstaller;
        $this->moduleList = $moduleList;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->customerSetup->run();
        if($this->moduleList->getModule('Magento_Review')) {
            $this->postInstaller->addSetupResource($this->reviewSetup);
        }
    }
}
