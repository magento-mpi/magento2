<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Sales;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Framework\App\State;
use Magento\Tools\SampleData\Helper\PostInstaller;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * Setup class for order
     *
     * @var Setup\Order
     */
    protected $orderSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param State $appState
     * @param Setup\Order $orderSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        State $appState,
        Setup\Order $orderSetup,
        PostInstaller $postInstaller

    ) {
        $this->appState = $appState;
        $this->orderSetup = $orderSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->orderSetup);
    }
}
