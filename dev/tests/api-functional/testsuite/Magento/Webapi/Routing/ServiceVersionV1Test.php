<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test routing based on Service Versioning(for V1 version of a service)
 */
namespace Magento\Webapi\Routing;

use Magento\Framework\Service\Data\Eav\AttributeValue;
use Magento\TestFramework\Authentication\OauthHelper;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModule1\Service\V1\Entity\ItemBuilder;

class ServiceVersionV1Test extends \Magento\Webapi\Routing\BaseService
{
    /**
     * @var string
     */
    protected $_version;
    /**
     * @var string
     */
    protected $_restResourcePath;
    /**
     * @var string
     */
    protected $_soapService = 'testModule1AllSoapAndRest';

    /** @var \Magento\Framework\Service\Data\Eav\AttributeValueBuilder */
    protected $valueBuilder;

    /** @var ItemBuilder */
    protected $itemBuilder;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testModule1AllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmodule1/";

        $this->valueBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\Data\Eav\AttributeValueBuilder'
        );

        $this->itemBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\ItemBuilder'
        );
    }

    /**
     *  Test get item
     */
    public function atestItem()
    {
        $itemId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Item']
        ];
        $requestData = ['itemId' => $itemId];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('testProduct1', $item['name'], 'Item was retrieved unsuccessfully');
}

    /**
     *  Test get item with any type
     */
    public function testItemAnyType()
    {
        $customerAttributes = [
            ItemBuilder::CUSTOM_ATTRIBUTE_1 => [
                AttributeValue::ATTRIBUTE_CODE => ItemBuilder::CUSTOM_ATTRIBUTE_1,
                AttributeValue::VALUE => '12345'
            ],
            ItemBuilder::CUSTOM_ATTRIBUTE_2 => [
                AttributeValue::ATTRIBUTE_CODE => ItemBuilder::CUSTOM_ATTRIBUTE_2,
                AttributeValue::VALUE => 12345
            ]
        ];

        $attributeValue1 = $this->valueBuilder
            ->populateWithArray($customerAttributes[ItemBuilder::CUSTOM_ATTRIBUTE_1])
            ->create();
        $attributeValue2 = $this->valueBuilder
            ->populateWithArray($customerAttributes[ItemBuilder::CUSTOM_ATTRIBUTE_2])
            ->create();

        $item = $this->itemBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttributes([$attributeValue1, $attributeValue2])
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $item = $this->_webApiCall($serviceInfo, ['item' => $requestData]);
        $this->assertSame($attributeValue1->getValue(), $item['custom_attributes'][0]['value']); // we should get string '12345'
        $this->assertSame($attributeValue2->getValue(), $item['custom_attributes'][1]['value']); // we should get integer 12345
    }

    /**
     * Test fetching all items
     */
    public function atestItems()
    {
        $itemArr = [['item_id' => 1, 'name' => 'testProduct1'], ['item_id' => 2, 'name' => 'testProduct2']];
        $serviceInfo = [
            'rest' => ['resourcePath' => $this->_restResourcePath, 'httpMethod' => RestConfig::HTTP_METHOD_GET],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Items']
        ];
        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals($itemArr, $item, 'Items were not retrieved');
    }

    /**
     *  Test create item
     */
    public function atestCreate()
    {
        $createdItemName = 'createdItemName';
        $serviceInfo = [
            'rest' => ['resourcePath' => $this->_restResourcePath, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Create']
        ];
        $requestData = ['name' => $createdItemName];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($createdItemName, $item['name'], 'Item creation failed');
    }

    /**
     *  Test create item with missing proper resources
     */
    public function atestCreateWithoutResources()
    {
        $createdItemName = 'createdItemName';
        $serviceInfo = [
            'rest' => ['resourcePath' => $this->_restResourcePath, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Create']
        ];
        $requestData = ['name' => $createdItemName];

        // getting new credentials that do not match the api resources
        OauthHelper::clearApiAccessCredentials();
        OauthHelper::getApiAccessCredentials([]);
        try {
            $this->assertUnauthorizedException($serviceInfo, $requestData);
        } catch (\Exception $e) {
            OauthHelper::clearApiAccessCredentials();
            throw $e;
        }
        // to allow good credentials to be restored (this is statically stored on OauthHelper)
        OauthHelper::clearApiAccessCredentials();
    }

    /**
     *  Test update item
     */
    public function atestUpdate()
    {
        $itemId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Update']
        ];
        $requestData = ['item' => ['itemId' => $itemId, 'name' => 'testName']];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('Updated' . $requestData['item']['name'], $item['name'], 'Item update failed');
    }

    /**
     *  Negative Test: Invoking non-existent delete api which is only available in V2
     */
    public function atestDelete()
    {
        $itemId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'Delete']
        ];
        $requestData = ['itemId' => $itemId, 'name' => 'testName'];
        $this->_assertNoRouteOrOperationException($serviceInfo, $requestData);
    }

    public function atestOverwritten()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'overwritten',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ]
        ];
        $item = $this->_webApiCall($serviceInfo, []);
        $this->assertEquals(['item_id' => -55, 'name' => 'testProduct1'], $item);
    }

    public function atestDefaulted()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'testOptionalParam',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ]
        ];
        $item = $this->_webApiCall($serviceInfo, []);
        $this->assertEquals(['item_id' => 3, 'name' => 'Default Name'], $item);
    }

    public function atestDefaultedWithValue()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'testOptionalParam',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ]
        ];
        $item = $this->_webApiCall($serviceInfo, ['name' => 'Ms. LaGrange']);
        $this->assertEquals(['item_id' => 3, 'name' => 'Ms. LaGrange'], $item);
    }
}
