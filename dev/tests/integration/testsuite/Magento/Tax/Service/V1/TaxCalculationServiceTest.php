<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\TestFramework\Helper\Bootstrap;

class TaxCalculationServiceTest
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Tax calculation model
     *
     * @var \Magento\Tax\Model\Calculation
     */
    private $calculator;

    /**
     * Tax configuration object
     *
     * @var \Magento\Tax\Model\Config
     */
    private $config;

    /**
     * Tax Helper
     *
     * @var \Magento\Tax\Helper\Data
     */
    private $helper;

    /**
     * Tax Details Builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\TaxDetailsBuilder
     */
    private $taxDetailsBuilder;

    /**
     * Tax Details Item Builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder
     */
    private $taxDetailsItemBuilder;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->calculator = $this
            ->objectManager
            ->create('Magento\Tax\Model\Calculation');
        $this->config = $this
            ->objectManager
            ->create('Magento\Tax\Model\Config');
        $this->helper = $this
            ->objectManager
            ->create('Magento\Tax\Helper\Data');
        $this->taxDetailsBuilder = $this
            ->objectManager
            ->create('Magento\Tax\Service\V1\Data\TaxDetails\TaxDetailsBuilder');
        $this->taxDetailsItemBuilder = $this
            ->objectManager
            ->create('Magento\Tax\Service\V1\data\TaxDetails\ItemBuilder');
    }




}
