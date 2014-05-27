<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Tax\Service\V1\Data\TaxRateBuilder;
use Magento\TestFramework\Helper\ObjectManager;

class TaxRateServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRateServiceInterface
     */
    private $taxRateService;

    /**
     * @var TaxRateBuilder
     */
    private $taxRateBuilder;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->taxRateService = $objectManager->getObject('Magento\Tax\Service\V1\TaxRateServiceInterface',
            []
        );
        $this->taxRateBuilder = $objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
    }
}
