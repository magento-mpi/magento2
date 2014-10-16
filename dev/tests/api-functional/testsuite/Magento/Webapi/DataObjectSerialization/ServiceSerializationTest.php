<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\DataObjectSerialization;

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
        $this->_restResourcePath = "/{$this->_version}/testmodule4/";
    }

    /**
     *  Test simple request data
     */
    public function atestGetServiceCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            )
        );
        $item = $this->_webApiCall($serviceInfo, array());
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    /**
     *  Test multiple params with Data Object
     */
    public function atestUpdateServiceCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            )
        );
        $item = $this->_webApiCall($serviceInfo, array('request' => array('name' => $name)));
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    /**
     *  Test nested Data Object
     */
    public function atestNestedDataObjectCall()
    {
        $itemId = 1;
        $name = 'Test';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId . '/nested',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            )
        );
        $item = $this->_webApiCall($serviceInfo, array('request' => array('details' => array('name' => $name))));
        $this->assertEquals($itemId, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }

    public function atestScalarResponse()
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

    public function testExtensibleCall()
    {
        $id = 2;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "{$this->_restResourcePath}extensibleDataObject/{$id}",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            )
        );

        $name = 'Magento';
        $requestData = [
          'name' => $name
        ];
        $item = $this->_webApiCall($serviceInfo, ['request' => $requestData]);
        $this->assertEquals($id, $item['entity_id'], 'id field returned incorrectly');
        $this->assertEquals($name, $item['name'], 'name field returned incorrectly');
    }
}
