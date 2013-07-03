<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sitemap_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var Saas_Sitemap_Helper_Data
     */
    protected $_helperData;

    public function setUp()
    {
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_ConfigInterface');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helperData = $objectManagerHelper->getObject('Saas_Sitemap_Helper_Data', array(
            'config' => $this->_storeConfigMock,
        ));
    }

    public function testGoogleVerificationCodeNotSet()
    {
        $value = '1234567';
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->with(Saas_Sitemap_Helper_Data::XML_PATH_GOOGLE_VERIFICATION_CODE)->will($this->returnValue($value));

        $this->assertEquals($value, $this->_helperData->getGoogleVerificationCode());
    }
}
