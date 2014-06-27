<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Service\V1\Data\Eav\Attribute;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class ReadServiceTest
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * Checks retrieving product attribute types
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
     * @dataProvider infoDataProvider
     * @param $attributeCode
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

    public function infoDataProvider()
    {
        return array(
            array('price'),
            array(95),
        );
    }

    /**
     * @dataProvider searchDataProvider
     */
    public function testSearch($filterGroups, $expectedAttributes, $sortData)
    {
        list($sortField, $sortValue) = $sortData;
        if ($sortValue === SearchCriteria::SORT_DESC && TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->markTestSkipped('Sorting doesn\'t work in SOAP');
        }
        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
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

        $searchCriteriaBuilder->setSortOrders([$sortField => $sortValue]);
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
                'operation' => self::SERVICE_NAME . 'search'
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
        foreach ($expectedAttributes as $expectedAttribute) {
            $this->assertContains($expectedAttribute, $searchResults['items']);
        }
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
                        [Attribute::FRONTEND_INPUT, 'textarea']
                    ],
                ],
                [
                    [
                        'code' => 'description',
                        'frontend_label' => 'Description',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'meta_keywords',
                        'frontend_label' => 'Meta Keywords',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'meta_description',
                        'frontend_label' => 'Meta Description',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'custom_layout_update',
                        'frontend_label' => 'Custom Layout Update',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'description',
                        'frontend_label' => 'Description',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'short_description',
                        'frontend_label' => 'Short Description',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'meta_keyword',
                        'frontend_label' => 'Meta Keywords',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'meta_description',
                        'frontend_label' => 'Meta Description',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                    [
                        'code' => 'custom_layout_update',
                        'frontend_label' => 'Custom Layout Update',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'textarea'
                    ],
                ],
                [Attribute::ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [Attribute::FRONTEND_INPUT, 'text']
                    ],
                    [ //Group(AND)
                        ['is_configurable', 1]
                    ],
                ],
                [
                    [
                        'code' => 'related_tgtr_position_limit',
                        'frontend_label' => 'Related Target Rule Rule Based Positions',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'text'
                    ],
                    [
                        'code' => 'related_tgtr_position_behavior',
                        'frontend_label' => 'Related Target Rule Position Behavior',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'text'
                    ],
                    [
                        'code' => 'upsell_tgtr_position_limit',
                        'frontend_label' => 'Upsell Target Rule Rule Based Positions',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'text'
                    ],
                    [
                        'code' => 'upsell_tgtr_position_behavior',
                        'frontend_label' => 'Upsell Target Rule Position Behavior',
                        'default_value' => null,
                        'is_required' => false,
                        'is_user_defined' => false,
                        'frontend_input' => 'text'
                    ],
                ],
                [Attribute::IS_REQUIRED, SearchCriteria::SORT_ASC]
            )
        );
    }
}
