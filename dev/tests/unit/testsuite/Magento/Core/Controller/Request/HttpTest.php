<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller\Request;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Controller\Request\Http */
    protected $_model;

    protected function setUp()
    {
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(),
            'Magento\Backend\Helper\DataProxy', false);
        $this->_model = new \Magento\Core\Controller\Request\Http($storeManager, $helperMock);
    }

    /**
     * @param $serverVariables array
     * @param $expectedResult string
     * @dataProvider serverVariablesProvider
     */
    public function testGetDistroBaseUrl($serverVariables, $expectedResult)
    {
        $originalServerValue = $_SERVER;
        $_SERVER = $serverVariables;

        $this->assertEquals($expectedResult, $this->_model->getDistroBaseUrl());

        $_SERVER = $originalServerValue;
    }

    public function serverVariablesProvider()
    {
        $returnValue = array();
        $defaultServerData = array(
            'SCRIPT_NAME' => 'index.php',
            'HTTP_HOST' => 'sample.host.com',
            'SERVER_PORT' => '80',
            'HTTPS' => '1',
        );

        $secureUnusualPort = $noHttpsData = $httpsOffData = $noHostData = $noScriptNameData = $defaultServerData;

        unset($noScriptNameData['SCRIPT_NAME']);
        $returnValue['no SCRIPT_NAME'] = array($noScriptNameData, 'http://localhost/');

        unset($noHostData['HTTP_HOST']);
        $returnValue['no HTTP_HOST'] = array($noHostData, 'http://localhost/');

        $httpsOffData['HTTPS'] = 'off';
        $returnValue['HTTPS off'] = array($httpsOffData, 'http://sample.host.com/');

        unset($noHttpsData['HTTPS']);
        $returnValue['no HTTPS'] = array($noHttpsData, 'http://sample.host.com/');

        $noHttpsNoServerPort = $noHttpsData;
        unset($noHttpsNoServerPort['SERVER_PORT']);
        $returnValue['no SERVER_PORT'] = array($noHttpsNoServerPort, 'http://sample.host.com/');

        $noHttpsButSecurePort = $noHttpsData;
        $noHttpsButSecurePort['SERVER_PORT'] = 443;
        $returnValue['no HTTP but secure port'] = array($noHttpsButSecurePort, 'https://sample.host.com/');

        $notSecurePort = $noHttpsData;
        $notSecurePort['SERVER_PORT'] = 81;
        $notSecurePort['HTTP_HOST'] = 'sample.host.com:81';
        $returnValue['not secure not standard port'] = array($notSecurePort, 'http://sample.host.com:81/');

        $secureUnusualPort['SERVER_PORT'] = 441;
        $secureUnusualPort['HTTP_HOST'] = 'sample.host.com:441';
        $returnValue['not standard secure port'] = array($secureUnusualPort, 'https://sample.host.com:441/');

        $customUrlPathData = $noHttpsData;
        $customUrlPathData['SCRIPT_FILENAME'] = '/some/dir/custom.php';
        $returnValue['custom path'] = array($customUrlPathData, 'http://sample.host.com/');

        return $returnValue;
    }
}
