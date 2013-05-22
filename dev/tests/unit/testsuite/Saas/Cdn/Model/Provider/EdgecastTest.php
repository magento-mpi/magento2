<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Cdn_Model_Provider_EdgecastTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configMock;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirMock;

    /**
     * @var Saas_Cdn_Model_Provider_Edgecast_Soap
     */
    protected $_soapMock;

    /**
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_logger = $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false);
        $this->_soapMock = $this->getMock(
            'Saas_Cdn_Model_Provider_Edgecast_Soap',
            array(
                'PurgeFileFromEdge',
                '__getLastResponse'
            ),
            array(),
            '',
            false
        );

        $connectionNode = new Mage_Core_Model_Config_Element(
            '
            <connection>
                <email>test@test.com</email>
                <password>123</password>
                <customer_id>1</customer_id>
                <wsdl_url>test://</wsdl_url>
            </connection>
            '
        );
        $map = array(
           array(Saas_Cdn_Model_Provider_Edgecast::XML_MEDIA, '', null, 'media_path'),
           array(Saas_Cdn_Model_Provider_Edgecast::XML_STATIC, '', null, 'static_path'),
           array(Saas_Cdn_Model_Provider_Edgecast_Soap::XML_CDN_CONNECTION_NODE, '', null, $connectionNode),
        );
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));

        $map = array(
           array(Mage_Core_Model_Dir::MEDIA, 'media_dir_path'),
           array(Mage_Core_Model_Dir::STATIC_VIEW, 'static_dir_path'),
        );
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));
    }

    /**
     * Test delete file from CDN success
     * @test
     */
    public function deleteFileSuccess()
    {
        $soapResult = (object)array(
            'PurgeFileFromEdgeResult' => 0,
        );
        $this->_soapMock->expects($this->any())->method('PurgeFileFromEdge')->will($this->returnValue($soapResult));

        $edgeCastProvider = new Saas_Cdn_Model_Provider_Edgecast(
            $this->_configMock,
            $this->_dirMock,
            $this->_logger,
            $this->_soapMock
        );

        $this->assertTrue($edgeCastProvider->deleteFile('media_dir_path'));
    }

    /**
     * In case of broken WSDL
     *
     * @test
     */
    public function deleteFileCdnExceptionCommunicationProblem()
    {
        $soapResult = (object)array();
        $faultString = '
            <response>
                <soapBody>
                    <soapFault>
                        <faultstring>error</faultstring>
                    </soapFault>
                </soapBody>
            </response>
        ';

        $this->_soapMock->expects($this->once())->method('PurgeFileFromEdge')->will($this->returnValue($soapResult));
        $this->_soapMock->expects($this->any())->method('__getLastResponse')->will($this->returnValue($faultString));

        $this->_logger->expects($this->once())->method('logException');


        $edgeCastProvider = new Saas_Cdn_Model_Provider_Edgecast(
            $this->_configMock,
            $this->_dirMock,
            $this->_logger,
            $this->_soapMock
        );

        $edgeCastProvider->deleteFile('media_dir_path');
    }

    /**
     * If Edge can't delete file (error, permission problem end etc.)
     *
     * @test
     */
    public function deleteFileCdnException()
    {
        $soapResult = (object)array(
            'PurgeFileFromEdgeResult' => 1,
        );

        $this->_soapMock->expects($this->any())->method('PurgeFileFromEdge')->will($this->returnValue($soapResult));
        $this->_logger->expects($this->once())->method('logException');

        $edgeCastProvider = new Saas_Cdn_Model_Provider_Edgecast(
            $this->_configMock,
            $this->_dirMock,
            $this->_logger,
            $this->_soapMock
        );

        $edgeCastProvider->deleteFile('media_dir_path');
    }

    public function tearDown()
    {
        $this->_configMock = null;
        $this->_dirMock = null;
    }
}
