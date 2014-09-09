<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Attribute;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ReadServiceTest
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
    public function attributeMetadataDataProvider() {
        return [
            [
                'msrp_enabled',
                [
                    'attribute_id' => '115',
                    'attribute_code' => 'msrp_enabled',
                    'frontend_input' => 'select',
                    'validation_rules' =>[],
                    'visible' => true,
                    'required' => false,
                    'options' =>
                    [
                        0 =>
                        [
                            'label' => 'Yes',
                            'value' => '1',
                        ],
                        1 =>
                        [
                            'label' => 'No',
                            'value' => '0',
                        ],
                        2 =>
                        [
                            'label' => 'Use config',
                            'value' => '2',
                        ],
                    ],
                    'user_defined' => false,
                    'frontend_label' =>
                    [
                    0 =>
                    [
                      'store_id' => '0',
                      'label' => 'Apply MAP',
                    ],
                    ],
                    'backend_type' => 'varchar',
                    'backend_model' => 'Magento\\Catalog\\Model\\Product\\Attribute\\Backend\\Msrp',
                    'source_model' => 'Magento\\Catalog\\Model\\Product\\Attribute\\Source\\Msrp\\Type\\Enabled',
                    'default_value' => '2',
                    'unique' => '0',
                    'apply_to' =>
                    [
                        0 => 'simple',
                        1 => 'bundle',
                        2 => 'virtual',
                        3 => 'downloadable',
                        4 => 'configurable',
                    ],
                    'searchable' => '0',
                    'visible_in_advanced_search' => '0',
                    'comparable' => '0',
                    'used_for_promo_rules' => '0',
                    'visible_on_front' => '0',
                    'used_in_product_listing' => '1',
                    'scope' => 'website',
                    'wysiwyg_enabled' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'position' => 0,
                ]
            ],
            [
                'allow_open_amount',
                [
                    'attribute_id' => '125',
                    'attribute_code' => 'allow_open_amount',
                    'frontend_input' => 'select',
                    'validation_rules' => [],
                    'visible' => true,
                    'required' => false,
                    'options' =>
                    [
                        0 =>
                        [
                            'label' => 'No',
                            'value' => '0',
                        ],
                        1 =>
                        [
                            'label' => 'Yes',
                            'value' => '1',
                        ],
                    ],
                    'user_defined' => false,
                    'frontend_label' =>
                    [
                        0 =>
                        [
                            'store_id' => '0',
                            'label' => 'Allow Open Amount',
                        ],
                    ],
                    'backend_type' => 'int',
                    'source_model' => 'Magento\\GiftCard\\Model\\Source\\Open',
                    'unique' => '0',
                    'apply_to' =>
                    [
                        0 => 'giftcard',
                    ],
                    'searchable' => '0',
                    'visible_in_advanced_search' => '0',
                    'comparable' => '0',
                    'used_for_promo_rules' => '0',
                    'visible_on_front' => '0',
                    'used_in_product_listing' => '1',
                    'scope' => 'website',
                    'wysiwyg_enabled' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'position' => 0,
                ]
            ],
            [
                'visibility',
                [
                    'attribute_id' => '100',
                    'attribute_code' => 'visibility',
                    'frontend_input' => 'select',
                    'validation_rules' => [],
                    'visible' => true,
                    'required' => false,
                    'options' =>
                    [
                        0 =>
                        [
                            'label' => 'Not Visible Individually',
                            'value' => '1',
                        ],
                        1 =>
                        [
                            'label' => 'Catalog',
                            'value' => '2',
                        ],
                        2 =>
                        [
                            'label' => 'Search',
                            'value' => '3',
                        ],
                        3 =>
                        [
                            'label' => 'Catalog, Search',
                            'value' => '4',
                        ],
                    ],
                    'user_defined' => false,
                    'frontend_label' =>
                    [
                    0 =>
                        [
                            'store_id' => '0',
                            'label' => 'Visibility',
                        ],
                    ],
                    'backend_type' => 'int',
                    'source_model' => 'Magento\\Catalog\\Model\\Product\\Visibility',
                    'default_value' => '4',
                    'unique' => '0',
                    'apply_to' => [],
                    'searchable' => '0',
                    'visible_in_advanced_search' => '0',
                    'comparable' => '0',
                    'used_for_promo_rules' => '0',
                    'visible_on_front' => '0',
                    'used_in_product_listing' => '0',
                    'scope' => 'store',
                    'wysiwyg_enabled' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'position' => 0,
                ]
            ],
            [
                'name',
                [
                    'attribute_id' => '71',
                    'attribute_code' => 'name',
                    'frontend_input' => 'text',
                    'validation_rules' => [],
                    'visible' => true,
                    'required' => true,
                    'options' => [],
                    'user_defined' => false,
                    'frontend_label' =>
                    [
                        0 =>
                        [
                            'store_id' => '0',
                            'label' => 'Name',
                        ],
                    ],
                    'backend_type' => 'varchar',
                    'unique' => '0',
                    'apply_to' => [],
                    'searchable' => '1',
                    'visible_in_advanced_search' => '1',
                    'comparable' => '0',
                    'used_for_promo_rules' => '0',
                    'visible_on_front' => '0',
                    'used_in_product_listing' => '1',
                    'scope' => 'store',
                    'frontend_class' => 'validate-length maximum-length-255',
                    'wysiwyg_enabled' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => true,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'position' => 0,
                ]
            ],
            [
                'sku',
                [
                    'attribute_id' => '72',
                    'attribute_code' => 'sku',
                    'frontend_input' => 'text',
                    'validation_rules' => [],
                    'visible' => true,
                    'required' => true,
                    'options' => [],
                    'user_defined' => false,
                    'frontend_label' =>
                    [
                        0 =>
                        [
                            'store_id' => '0',
                            'label' => 'SKU',
                        ],
                    ],
                    'backend_type' => 'static',
                    'backend_model' => 'Magento\\Catalog\\Model\\Product\\Attribute\\Backend\\Sku',
                    'unique' => '1',
                    'apply_to' => [],
                    'searchable' => '1',
                    'visible_in_advanced_search' => '1',
                    'comparable' => '1',
                    'used_for_promo_rules' => '0',
                    'visible_on_front' => '0',
                    'used_in_product_listing' => '0',
                    'scope' => 'global',
                    'frontend_class' => 'validate-length maximum-length-64',
                    'wysiwyg_enabled' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'position' => 0,
                ],
                [
                    'Not Existing Attribute Code',
                    [
                        'attribute_code' => 'Not Existing Attribute Code',
                        'validation_rules' => [],
                        'options' => [],
                        'frontend_label' =>
                        [
                        0 =>
                            [
                                'store_id' => '0',
                                'label' => '',
                            ],
                        ],
                        'scope' => 'store',
                    ]
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
     * @dataProvider attributeMetadataDataProvider
     */
    public function testOptions($id, $expectedAttributeMetadata)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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

        //$this->assertEquals($expectedAttributeMetadata['options'], $actualResult);
        //^^^^^^^^^^^^^^^^^^^^^^^Doesn't work in REST test^^^^^^^^^^^^^^^^^^^^^^^^^^
    }

    /**
     * Test retrieve attribute info
     *
     * @param string $id Attribute ID/code
     * @param array $expectedResult Expected attribute metadata
     *
     * @dataProvider attributeMetadataDataProvider
     */
    public function testInfo($id, $expectedResult)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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
        //$this->assertEquals($expectedResult, $actualResult);
        //^^^^^^^^^^^^^Doesn't work in REST test^^^^^^^^^^^^^^
    }

    public function testSearch()
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder =  Bootstrap::getObjectManager()
            ->create('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder');
        $filter = $filterBuilder->setField('code')->setValue('name')->create();
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

        //^^^^^^^^^^^^^API call Produces ERROR^^^^^^^^^^^^^^
    }
}
