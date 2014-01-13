<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Routing;

use Magento\Webapi\Model\Rest\Config as RestConfig;

class GettersTest extends \Magento\Webapi\Routing\BaseService
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
    protected $_soapService = 'testModule5AllSoapAndRest';

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testModule5AllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmodule5/";
    }

    public function testGetters()
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
        $requestData = array('itemId' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], 'Item was retrieved unsuccessfully');
        $isEnabled = isset($item['isEnabled']) && $item['isEnabled'] === true;
        $this->assertTrue($isEnabled, 'Getter with "is" prefix is processed incorrectly.');
        $hasName = isset($item['hasName']) && $item['hasName'] === true;
        $this->assertTrue($hasName, 'Getter with "has" prefix is processed incorrectly.');
    }
}
