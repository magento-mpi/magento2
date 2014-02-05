<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

    protected function setUp()
    {
        $this->_markTestAsRestOnly();
        $this->_version = 'V1';
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
            )
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
            )
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
            )
        );
        $item = $this->_webApiCall($serviceInfo, ['request' => ['details' => ['name' => $name]]]);
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    public function testScalarResponse()
    {
        $id = 2;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "{$this->_restResourcePath}scalar/{$id}",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            )
        );
        $this->assertEquals($id, $this->_webApiCall($serviceInfo), 'Scalar service output is serialized incorrectly.');
    }
}
