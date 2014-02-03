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

use Magento\TestFramework\Authentication\OauthHelper;
use Magento\Webapi\Model\Rest\Config as RestConfig;

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

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testModule1AllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmodule1/";
    }


    /**
     *  Test get item
     */
    public function testItem()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
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
     * Test fetching all items
     */
    public function testItems()
    {
        $itemArr = array(
            array(
                'id' => 1,
                'name' => 'testProduct1'
            ),
            array(
                'id' => 2,
                'name' => 'testProduct2'
            )
        );
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Items'
            )
        );
        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals($itemArr, $item, 'Items were not retrieved');
    }

    /**
     *  Test create item
     */
    public function testCreate()
    {
        $createdItemName = 'createdItemName';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($createdItemName, $item['name'], 'Item creation failed');
    }

    /**
     *  Test create item with missing proper resources to fail AuthZ
     */
    public function testCreateWithoutResources()
    {
        $createdItemName = 'createdItemName';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);

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
    public function testUpdate()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Update'
            )
        );
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('Updated' . $requestData['name'], $item['name'], 'Item update failed');
    }

    /**
     *  Negative Test: Invoking non-existent delete api which is only available in V2
     */
    public function testDelete()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Delete'
            )
        );
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $this->_assertNoRouteOrOperationException($serviceInfo, $requestData);
    }
}
