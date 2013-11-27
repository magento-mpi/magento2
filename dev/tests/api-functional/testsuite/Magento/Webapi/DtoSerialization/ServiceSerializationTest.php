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
namespace Magento\Webapi\DtoSerialization;

class ServiceSerializationTest extends \Magento\TestFramework\TestCase\WebapiAbstract
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

    protected function setUp()
    {
        $this->_markTestAsRestOnly();
        $this->_version = 'V1';
        //$this->_soapService = 'testModule1AllSoapAndRestV1';
        $this->_restResourcePath = "/$this->_version/testmodule4/";
    }


    /**
     *  Test simple request data
     */
    public function testGetServiceCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            //'soap' => array(
            //    'service' => $this->_soapService,
            //    'operation' => $this->_soapService . 'Item'
            //)
        );
        $item = $this->_webApiCall($serviceInfo, []);
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    /**
     *  Test multiple params with DTO
     */
    public function testUpdateServiceCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ),
            //'soap' => array(
            //    'service' => $this->_soapService,
            //    'operation' => $this->_soapService . 'Item'
            //)
        );
        $item = $this->_webApiCall($serviceInfo, ['request' => ['name' => $name]]);
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    /**
     *  Test nested DTO
     */
    public function testNestedDtoCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId.'/nested',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ),
            //'soap' => array(
            //    'service' => $this->_soapService,
            //    'operation' => $this->_soapService . 'Item'
            //)
        );
        $item = $this->_webApiCall($serviceInfo, ['request' => ['details' => ['name' => $name]]]);
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }
}
