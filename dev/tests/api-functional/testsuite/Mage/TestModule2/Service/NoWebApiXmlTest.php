<?php
/**
 * Test NoWebApiXmlTestTest TestModule2
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_NoWebApiXmlTestTest extends Magento_Test_TestCase_WebapiAbstract
{
    /**
     * @var string
     */
    private $_version;
    /**
     * @var string
     */
    private $_restResourcePath;
    /**
     * @var string
     */
    private $_soapService;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/$this->_version/testModule2NoWebApiXml/";
        $this->_soapService = 'testModule2NoWebApiXml';
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
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertAdapterException($serviceInfo, $requestData);
    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Items'
            )
        );
        $this->assertAdapterException($serviceInfo);
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
                'httpMethod' => 'POST'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);
        $this->assertAdapterException($serviceInfo, $requestData);
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
                'httpMethod' => 'PUT'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Update'
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertAdapterException($serviceInfo, $requestData);
    }

    /**
     *  Test remove item
     */
    public function testRemove()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => 'DELETE'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Remove'
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertAdapterException($serviceInfo, $requestData);
    }

    /**
     * Utility to check a particular adapter and assert the exception
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertAdapterException($serviceInfo, $requestData = null)
    {
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->assertSoapException($serviceInfo, $requestData);
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo, $requestData);
        }
    }

    /**
     * This is a helper function to invoke the REST api and assert for the AllSoapNoRestV1Test
     * test cases that no such REST route exist
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertRestException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (Exception $e) {
            $this->assertEquals(
                $e->getMessage(),
                '{"errors":[{"code":404,"message":"Request does not match any route."}]}',
                sprintf(
                    'REST routing did not fail as expected for Resource "%s" and method "%s"',
                    $serviceInfo['rest']['resourcePath'],
                    $serviceInfo['rest']['httpMethod']
                )
            );
        }
    }

    /**
     * TODO: Temporary Exception assertion. Need to refine
     * This is a helper function to invoke the SOAP api and assert for the NoWebApiXmlTestTest
     * test cases that no such SOAP route exists
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertSoapException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (Exception $e) {
            $this->assertEquals(
                get_class($e),
                'SoapFault',
                sprintf(
                    'Expected SoapFault exception not generated for
                    Service - "%s" and serviceVersion - "%s" and Operation - "%s"',
                    $serviceInfo['soap']['service'],
                    $serviceInfo['soap']['serviceVersion'],
                    $serviceInfo['soap']['operation']
                )
            );
        }
    }
}
