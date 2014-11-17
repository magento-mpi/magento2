<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Tax\Model\ClassModelRegistry;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\Data\TaxClassBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Tests for tax class service.
 */
class TaxClassServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'taxTaxClassServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/taxClass';

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var TaxClassBuilder */
    private $taxClassBuilder;

    /** @var TaxClassService */
    private $taxClassService;

    /** @var ClassModelRegistry */
    private $taxClassRegistry;

    const SAMPLE_TAX_CLASS_NAME = 'Wholesale Customer';

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        );
        $this->filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\FilterBuilder'
        );
        $this->taxClassBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Tax\Service\V1\Data\TaxClassBuilder'
        );
        $this->taxClassRegistry = Bootstrap::getObjectManager()->create(
            'Magento\Tax\Model\ClassModelRegistry'
        );
        $this->taxClassService = Bootstrap::getObjectManager()->create(
            'Magento\Tax\Service\V1\TaxClassService',
            ['classModelRegistry' => $this->taxClassRegistry]
        );
    }

    /**
     * Test create TaxClass
     */
    public function testCreateTaxClass()
    {
        $taxClassName = self::SAMPLE_TAX_CLASS_NAME . uniqid();
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassServiceInterface::TYPE_CUSTOMER)
            ->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateTaxClass'
            ]
        ];

        $requestData = ['taxClass' => $taxClassDataObject->__toArray()];
        $taxClassId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($taxClassId);

        //Verify by getting the TaxClass
        $taxClassData = $this->taxClassService->getTaxClass($taxClassId);
        $this->assertEquals($taxClassData->getClassName(), $taxClassName);
        $this->assertEquals($taxClassData->getClassType(), TaxClassServiceInterface::TYPE_CUSTOMER);
    }

    /**
     * Test create TaxClass
     */
    public function testUpdateTaxClass()
    {
        //Create Tax Class
        $taxClassDataObject = $this->taxClassBuilder->setClassName(self::SAMPLE_TAX_CLASS_NAME . uniqid())
            ->setClassType(TaxClassServiceInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertNotNull($taxClassId);

        //Update Tax Class
        $updatedTaxClassName = self::SAMPLE_TAX_CLASS_NAME . uniqid();
        $updatedTaxClassDataObject = $this->taxClassBuilder
            ->populate($taxClassDataObject)
            ->setClassName($updatedTaxClassName)
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $taxClassId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateTaxClass'
            ]
        ];

        $requestData = ['taxClass' => $updatedTaxClassDataObject->__toArray(), 'taxClassId' => $taxClassId];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        //Verify by getting the TaxClass
        $this->taxClassRegistry->remove($taxClassId);
        $taxClassData = $this->taxClassService->getTaxClass($taxClassId);
        $this->assertEquals($taxClassData->getClassName(), $updatedTaxClassName);
    }

    public function testGetTaxClass()
    {
        //Create Tax Class
        $taxClassName = self::SAMPLE_TAX_CLASS_NAME . uniqid();
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassServiceInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertNotNull($taxClassId);

        //Verify by getting the TaxClass
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $taxClassId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTaxClass'
            ]
        ];
        $requestData = ['taxClassId' => $taxClassId];
        $taxClassData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($taxClassData[TaxClass::KEY_NAME], $taxClassName);
        $this->assertEquals($taxClassData[TaxClass::KEY_TYPE], TaxClassServiceInterface::TYPE_CUSTOMER);
    }

    /**
     * Test delete Tax class
     */
    public function testDeleteTaxClass()
    {
        $taxClassDataObject = $this->taxClassBuilder->setClassName(self::SAMPLE_TAX_CLASS_NAME . uniqid())
            ->setClassType(TaxClassServiceInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertNotNull($taxClassId);

        //Verify by getting the TaxClass
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $taxClassId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteTaxClass'
            ]
        ];
        $requestData = ['taxClassId' => $taxClassId];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);

        try {
            $this->taxClassRegistry->remove($taxClassId);
            $this->taxClassService->getTaxClass($taxClassId);
            $this->fail("Tax class was not expected to be returned after being deleted.");
        } catch (NoSuchEntityException $e) {
            $this->assertEquals('No such entity with class_id = ' . $taxClassId, $e->getMessage());
        }
    }

    /**
     * Test with a single filter
     */
    public function testSearchTaxClass()
    {
        $this->markTestSkipped('Should be enabled after fixing MAGETWO-29964');

        $taxClassName = 'Retail Customer';
        $taxClassNameField = TaxClass::KEY_NAME;
        $filter = $this->filterBuilder->setField($taxClassNameField)
            ->setValue($taxClassName)
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter]);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchTaxClass'
            ]
        ];
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($taxClassName, $searchResults['items'][0][$taxClassNameField]);
    }

    /**
     * Test using multiple filters
     */
    public function testSearchTaxClassMultipleFilterGroups()
    {
        $this->markTestSkipped('Should be enabled after fixing MAGETWO-29964');

        $productTaxClass = [TaxClass::KEY_NAME => 'Taxable Goods', TaxClass::KEY_TYPE => 'PRODUCT'];
        $customerTaxClass = [TaxClass::KEY_NAME => 'Retail Customer', TaxClass::KEY_TYPE => 'CUSTOMER'];

        $filter1 = $this->filterBuilder->setField(TaxClass::KEY_NAME)
            ->setValue($productTaxClass[TaxClass::KEY_NAME])
            ->create();
        $filter2 = $this->filterBuilder->setField(TaxClass::KEY_NAME)
            ->setValue($customerTaxClass[TaxClass::KEY_NAME])
            ->create();
        $filter3 = $this->filterBuilder->setField(TaxClass::KEY_TYPE)
            ->setValue($productTaxClass[TaxClass::KEY_TYPE])
            ->create();
        $filter4 = $this->filterBuilder->setField(TaxClass::KEY_TYPE)
            ->setValue($customerTaxClass[TaxClass::KEY_TYPE])
            ->create();

        /**
         * (class_name == 'Retail Customer' || class_name == 'Taxable Goods)
         * && ( class_type == 'CUSTOMER' || class_type == 'PRODUCT')
         */
        $this->searchCriteriaBuilder->addFilter([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter([$filter3, $filter4]);
        $searchCriteria = $this->searchCriteriaBuilder->setCurrentPage(1)->setPageSize(10)->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchTaxClass'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(2, $searchResults['total_count']);
        $this->assertEquals($productTaxClass[TaxClass::KEY_NAME], $searchResults['items'][0][TaxClass::KEY_NAME]);
        $this->assertEquals($customerTaxClass[TaxClass::KEY_NAME], $searchResults['items'][1][TaxClass::KEY_NAME]);

        /** class_name == 'Retail Customer' && ( class_type == 'CUSTOMER' || class_type == 'PRODUCT') */
        $this->searchCriteriaBuilder->addFilter([$filter2]);
        $this->searchCriteriaBuilder->addFilter([$filter3, $filter4]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerTaxClass[TaxClass::KEY_NAME], $searchResults['items'][0][TaxClass::KEY_NAME]);
    }
}
