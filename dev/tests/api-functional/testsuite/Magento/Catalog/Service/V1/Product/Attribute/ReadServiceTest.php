<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Framework\Api\SearchCriteria;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ReadServiceTest
 *
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeReadServiceV1Types'
            ],
        ];

        $types = $this->_webApiCall($serviceInfo);
        $this->assertGreaterThan(0, count($types), "The number of product attribute types returned is zero.");
    }

    /**
     * @param string $attributeCode
     * @dataProvider infoDataProvider
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testInfo($attributeCode)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Info'
            ],
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, array('id' => $attributeCode));
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('attribute_id', $response);
        $this->assertArrayHasKey('attribute_code', $response);
    }

    /**
     * @return array
     */
    public function infoDataProvider()
    {
        return [
            ['test_attribute_code_333'],
            [95],
        ];
    }

    /**
     * @dataProvider searchDataProvider
     */
    public function testSearch($filterGroups, $expectedAttributes, $sortData)
    {
        $this->markTestIncomplete(
            'The test relies on system state that is incorrect as it is very fragile. '
            . 'It must prepare its own data to rely on.'
        );
        list($sortField, $sortValue) = $sortData;
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->markTestSkipped('Sorting doesn\'t work in SOAP');
        }
        /** @var $searchCriteriaBuilder  \Magento\Framework\Api\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        );
        /** @var $filterBuilder  \Magento\Framework\Api\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\FilterBuilder'
        );
        foreach ($filterGroups as $filterGroup) {
            $group = array();
            foreach ($filterGroup as $filter) {
                list($filterKey, $filterValue) = $filter;
                $group[] = $filterBuilder
                    ->setField($filterKey)
                    ->setValue($filterValue)
                    ->create();
            }
            $searchCriteriaBuilder->addFilter($group);
        }
        /**@var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SortOrderBuilder'
        );
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField($sortField)->setDirection($sortValue)->create();
        $searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $searchData = $searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Search'
            ]
        ];

        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $searchResults);
        $this->assertEquals(count($expectedAttributes), count($searchResults['items']));
        $this->assertEquals(count($expectedAttributes), $searchResults['total_count']);
        // Prepare actual data for check
        foreach ($searchResults['items'] as &$attribute) {
            // Add empty default values because SOAP service does not return null values
            if (!isset($attribute['default_value'])) {
                $attribute['default_value'] = null;
            }
            // Remove attribute IDs (in order to make test more clear i.e. without hardcoded IDs)
            unset($attribute['id']);
        }
        $this->assertEquals(
            array_map(
                function ($i) {
                    return $i['code'];
                },
                $expectedAttributes
            ),
            array_map(
                function ($i) {
                    return $i['attribute_code'];
                },
                $searchResults['items']
            )
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function searchDataProvider()
    {
        return array(
            array(
                [ //Groups
                    [ //Group(AND)
                        [AttributeMetadata::FRONTEND_INPUT, 'textarea']
                    ],
                ],
                [
                    [
                        'code' => 'description',
                    ],
                    [
                        'code' => 'short_description',
                    ],
                    [
                        'code' => 'meta_keyword',
                    ],
                    [
                        'code' => 'meta_description',
                    ],
                    [
                        'code' => 'custom_layout_update',
                    ],
                ],
                [AttributeMetadata::ATTRIBUTE_ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [AttributeMetadata::FRONTEND_INPUT, 'text']
                    ],
                    [ //Group(AND)
                        ['is_configurable', 1]
                    ],
                ],
                [
                    [
                        'code' => 'related_tgtr_position_behavior',
                    ],
                    [
                        'code' => 'related_tgtr_position_limit',
                    ],
                    [
                        'code' => 'upsell_tgtr_position_behavior',
                    ],
                    [
                        'code' => 'upsell_tgtr_position_limit',
                    ],
                ],
                [AttributeMetadata::REQUIRED, SearchCriteria::SORT_ASC]
            )
        );
    }
}
