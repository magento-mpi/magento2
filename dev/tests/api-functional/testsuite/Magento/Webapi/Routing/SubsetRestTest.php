<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test routing based on a Service that exposes subset of REST Operations
 */
namespace Magento\Webapi\Routing;

class SubsetRestTest extends \Magento\Webapi\Routing\SoapOnlyTest
{
    /**
     * @Override
     */
    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/$this->_version/testModule2SubsetRest/";
        $this->_soapService = 'testModule2SubsetRestV1';
    }

    /**
     * @Override
     * Test get item
     */
    public function testItem()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], 'Item was retrieved unsuccessfully');
    }

    /**
     * @Override
     * Test fetching all items
     */
    public function testItems()
    {
        $itemArr = array(
            array(
                'id' => 1,
                'name' => 'testItem1'
            ),
            array(
                'id' => 2,
                'name' => 'testItem2'
            )
        );
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Items'
            )
        );

        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals($itemArr, $item, 'Items were not retrieved');
    }
}
