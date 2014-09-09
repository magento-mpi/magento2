<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Routing;

class ServiceVersionV2Test extends \Magento\Webapi\Routing\BaseService
{
    protected function setUp()
    {
        $this->_version = 'V2';
        $this->_soapService = 'testModule1AllSoapAndRestV2';
        $this->_restResourcePath = "/{$this->_version}/testmodule1/";
    }

    /**
     *  Test to assert overriding of the existing 'Item' api in V2 version of the same service
     */
    public function testItem()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array('service' => $this->_soapService, 'operation' => $this->_soapService . 'Item')
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        // Asserting for additional attribute returned by the V2 api
        $this->assertEquals(1, $item['price'], 'Item was retrieved unsuccessfully from V2');
    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
        $itemArr = array(
            array('id' => 1, 'name' => 'testProduct1', 'price' => '1'),
            array('id' => 2, 'name' => 'testProduct2', 'price' => '2')
        );
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array('service' => $this->_soapService, 'operation' => $this->_soapService . 'Items')
        );
        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals($itemArr, $item, 'Items were not retrieved');
    }

    /**
     * Test fetching items when filters are applied
     *
     * @param string[] $filters
     * @param array $expectedResult
     * @dataProvider itemsWithFiltersDataProvider
     */
    public function testItemsWithFilters($filters, $expectedResult)
    {
        $restFilter = '';
        foreach ($filters as $filterItemKey => $filterMetadata) {
            foreach ($filterMetadata as $filterMetaKey => $filterMetaValue) {
                $paramsDelimiter = empty($restFilter) ? '?' : '&';
                $restFilter .= "{$paramsDelimiter}filters[{$filterItemKey}][{$filterMetaKey}]={$filterMetaValue}";
            }
        }
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $restFilter,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Items'
            )
        );
        $requestData = [];
        if (!empty($filters)) {
            $requestData['filters'] = $filters;
        }
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($expectedResult, $item, 'Filtration does not seem to work correctly.');
    }

    public function itemsWithFiltersDataProvider()
    {

        $firstItem = ['id' => 1, 'name' => 'testProduct1', 'price' => 1];
        $secondItem = ['id' => 2, 'name' => 'testProduct2', 'price' => 2];
        return [
            'Both items filter' => [
                [
                    ['field' => 'id', 'conditionType' => 'eq','value' => 1],
                    ['field' => 'id', 'conditionType' => 'eq','value' => 2]
                ],
                [$firstItem, $secondItem]
            ],
            'First item filter' => [[['field' => 'id', 'conditionType' => 'eq','value' => 1]], [$firstItem]],
            'Second item filter' => [[['field' => 'id', 'conditionType' => 'eq','value' => 2]], [$secondItem]],
            'Empty filter' => [[], [$firstItem, $secondItem]],
        ];
    }

    /**
     *  Test update item
     */
    public function testUpdate()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ),
            'soap' => array('service' => $this->_soapService, 'operation' => $this->_soapService . 'Update')
        );
        $requestData = array('entityItem' => array('id' => $itemId, 'name' => 'testName', 'price' => '4'));
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('Updated' . $requestData['entityItem']['name'], $item['name'], 'Item update failed');
    }

    /**
     *  Test to assert presence of new 'delete' api added in V2 version of the same service
     */
    public function testDelete()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ),
            'soap' => array('service' => $this->_soapService, 'operation' => $this->_soapService . 'Delete')
        );
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item delete failed");
    }
}
