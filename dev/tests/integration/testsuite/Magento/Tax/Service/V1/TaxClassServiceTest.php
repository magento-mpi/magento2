<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Service\V1;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Integration test for service layer
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoAppArea frontend
 */
class TaxClassServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var TaxClassServiceInterface */
    private $_taxClassService;

    /** @var \Magento\Framework\ObjectManager */
    private $_objectManager;

    /** @var \Magento\Tax\Service\V1\Data\TaxClass */
    private $_taxClass;

    protected function setUp()
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_taxClassService = $this->_objectManager
            ->create('Magento\Tax\Service\V1\TaxClassService');

        $taxClassBuilder = $this->_objectManager->create('Magento\Tax\Service\V1\Data\TaxClassBuilder');
        $this->_taxClass = $taxClassBuilder->create(); 
    }

    /**
     * Clean up shared dependencies
     */
    protected function tearDown()
    {

    }

    /**
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testDeleteCustomer()
    {
        $this->assertTrue($this->_taxClassService->deleteTaxClass(4));
        // Verify if the tax class is deleted
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with taxClassId = 4'
        );
        $this->_taxClassService->deleteTaxClass(4);
    }

}
