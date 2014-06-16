<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontResolverMock;

    protected function setUp()
    {
        $this->_frontResolverMock = $this->getMock(
            '\Magento\Backend\App\Area\FrontNameResolver',
            array(),
            array(),
            '',
            false
        );
        $this->_helper = new \Magento\Backend\Helper\Data(
            $this->getMock('Magento\Framework\App\Helper\Context', array(), array(), '', false, false),
            $this->getMock('\Magento\Framework\App\Route\Config', array(), array(), '', false),
            $this->getMock('Magento\Framework\Locale\ResolverInterface'),
            $this->getMock('\Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('\Magento\Backend\Model\Auth', array(), array(), '', false),
            $this->_frontResolverMock,
            $this->getMock('\Magento\Framework\Math\Random', array(), array(), '', false),
            $this->getMock('\Magento\Framework\App\RequestInterface')
        );
    }

    public function testGetAreaFrontNameLocalConfigCustomFrontName()
    {
        $this->_frontResolverMock->expects(
            $this->once()
        )->method(
            'getFrontName'
        )->will(
            $this->returnValue('custom_backend')
        );

        $this->assertEquals('custom_backend', $this->_helper->getAreaFrontName());
    }

    /**
     * @param array $inputString
     * @param array $expected
     *
     * @dataProvider getPrepareFilterStringValuesDataProvider
     */
    public function testPrepareFilterStringValues(array $inputString, array $expected)
    {
        $inputString = base64_encode(http_build_query($inputString));

        $actual = $this->_helper->prepareFilterString($inputString);

        $this->assertEquals($expected, $actual);
    }

    public function getPrepareFilterStringValuesDataProvider()
    {
        return array(
            'left_space_value' => array(
                array('field' => ' value'),
                array('field' => 'value')
            ),
            'right_space_value' => array(
                array('field' => 'value '),
                array('field' => 'value')
            ),
            'both_spaces_value' => array(
                array('field' => ' value '),
                array('field' => 'value')
            )
        );
    }
}
