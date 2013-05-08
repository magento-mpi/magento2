<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sitemap_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Saas_Sitemap_Block_Html_Head
     */
    protected $_blockHead;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Saas_Sitemap_Helper_Data', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_blockHead = $objectManagerHelper->getObject('Saas_Sitemap_Block_Html_Head', array(
            'helper' => $this->_helperMock,
        ));
    }

    public function testGoogleVerificationCodeNotSet()
    {
        $value = '1234567';
        $this->_helperMock->expects($this->once())->method('getGoogleVerificationCode')
            ->will($this->returnValue($value));

        $this->assertEquals($value, $this->_blockHead->getGoogleVerificationCode());
    }
}
