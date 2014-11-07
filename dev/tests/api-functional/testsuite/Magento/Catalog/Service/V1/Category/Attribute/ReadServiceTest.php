<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Attribute;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

/**
 * Class ReadServiceTest
 *
 * @package Magento\Catalog\Service\V1\Category\Attribute
 */
class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'catalogCategoryAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories/attributes';

    /**
     * Data provider
     */
    public function categoryAttributesDataProvider()
    {
        return
            [
                [
                    'display_mode',
                    [
                        [
                            'value' => 'PRODUCTS',
                            'label' => 'Products only',
                        ],
                        [
                            'value' => 'PAGE',
                            'label' => 'Static block only',
                        ],
                        [
                            'value' => 'PRODUCTS_AND_PAGE',
                            'label' => 'Static block and products',
                        ]
                    ]
                ],
                [
                    'is_active',
                    [
                        [
                            'value' => '1',
                            'label' => 'Yes',
                        ],
                        [
                            'value' => '0',
                            'label' => 'No',
                        ],
                    ]
                ]
            ];
    }

    /**
     * Test retrieve attribute options
     *
     * @param string $id Attribute ID/code
     * @param array $expectedAttributeMetadata Expected attribute metadata
     *
     * @dataProvider categoryAttributesDataProvider
     */
    public function testOptions($id, $expectedAttributeMetadata)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id . '/options',
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'options'
            ]
        ];
        $actualResult = $this->_webApiCall($serviceInfo, ['id' => $id]);
        $this->assertEquals($expectedAttributeMetadata, $actualResult);
    }

    /**
     * Data provider
     */
    public function attributeOptionsDataProvider()
    {
        return
            [
                [
                    'name',
                    [
                        'attribute_code' => 'name',
                        'backend_model' => '',
                        'backend_type' => 'varchar',
                        'frontend_input' => 'text',
                        'frontend_label' =>
                            [
                                0 =>
                                    [
                                        'store_id' => '0',
                                        'label' => 'Name',
                                    ],
                            ],
                        'frontend_class' => '',
                        'source_model' => '',
                        'default_value' => '',
                        'note' => '',
                        'used_in_product_listing' => '0',
                        'used_for_sort_by' => '0',
                        'apply_to' => [],
                        'position' => 0,
                        'required' => '1',
                        'user_defined' => '0',
                        'unique' => '0',
                        'visible' => '1',
                        'searchable' => '0',
                        'filterable' => '0',
                        'comparable' => '0',
                        'visible_on_front' => '0',
                        'html_allowed_on_front' => '0',
                        'filterable_in_search' => '0',
                        'visible_in_advanced_search' => '0',
                        'wysiwyg_enabled' => '0',
                        'used_for_promo_rules' => '0',
                        'configurable' => '1',
                        'options' => [],
                        'validation_rules' => [],
                        'scope' => 'store'
                    ]
                ]
            ];
    }

    /**
     * Test retrieve attribute info
     *
     * @param string $id Attribute ID/code
     * @param array $expectedResult Expected attribute metadata
     *
     * @dataProvider attributeOptionsDataProvider
     */
    public function testInfo($id, $expectedResult)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-29118");
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'info'
            ]
        ];

        $requestData = ['id' => $id];
        $actualResult = $this->_webApiCall($serviceInfo, $requestData);
        unset($actualResult['attribute_id']);
        ksort($actualResult);
        ksort($expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSearch()
    {
        /** @var $filterBuilder  \Magento\Framework\Api\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        /** @var $searchCriteriaBuilder  \Magento\Framework\Api\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $attributeCode = 'name';
        $filter = $filterBuilder->setField('code')->setValue($attributeCode)->create();
        $searchCriteriaBuilder->addFilter([$filter]);
        $searchData = $searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'search'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $result['total_count']);
        $this->assertEquals($attributeCode, $result['items'][0]['attribute_code']);
    }
}
