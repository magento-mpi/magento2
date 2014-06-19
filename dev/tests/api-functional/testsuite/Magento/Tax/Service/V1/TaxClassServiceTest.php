<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Tax\Service\V1\Data\TaxClass;
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

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        $this->filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );
    }

    /**
     * Test with a single filter
     */
    public function testSearchTaxClass()
    {
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
