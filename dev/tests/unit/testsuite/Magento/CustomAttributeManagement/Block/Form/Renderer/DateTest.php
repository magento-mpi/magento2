<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomAttributeManagement\Block\Form\Renderer\Date
     */
    protected $_block;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeDateMock;

    protected function setUp()
    {
        $contextMock = $this->getMock(
            'Magento\Framework\View\Element\Template\Context', array('getLocaleDate'), array(), '', false
        );

        $this->_localeDateMock = $this->getMockForAbstractClass('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $contextMock->expects($this->once())
            ->method('getLocaleDate')
            ->will($this->returnValue($this->_localeDateMock));

        $this->_block = new Date($contextMock);
    }

    public function testGetDateFormat()
    {
        $this->_localeDateMock->expects($this->once())
            ->method('getDateFormat')
            ->with(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT)
            ->will($this->returnArgument(0));

        $this->assertEquals(
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
            $this->_block->getDateFormat()
        );
    }

    /**
     * @param string $expected
     * @param array $data
     * @dataProvider getSortedDateInputsDataProvider
     */
    public function testGetSortedDateInputs($expected, array $data)
    {
        $this->_localeDateMock->expects($this->once())
            ->method('getDateFormat')
            ->with(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT)
            ->will($this->returnValue($data['format']));

        foreach ($data['date_inputs'] as $code => $html) {
            $this->_block->setDateInput($code, $html);
        }
        $this->assertEquals($expected, $this->_block->getSortedDateInputs($data['strip_non_input_chars']));
    }

    /**
     * @return array
     */
    public function getSortedDateInputsDataProvider()
    {
        return array(
            array(
                '<y><d><d><m>',
                array(
                    'strip_non_input_chars' => true,
                    'date_inputs' => array(
                        'm' => '<m>',
                        'd' => '<d>',
                        'y' => '<y>',
                    ),
                    'format' => 'y--d--e--m'
                )
            ),
            array(
                '<y>--<d>--<d>--<m>',
                array(
                    'strip_non_input_chars' => false,
                    'date_inputs' => array(
                        'm' => '<m>',
                        'd' => '<d>',
                        'y' => '<y>',
                    ),
                    'format' => 'y--d--e--m'
                )
            ),

            array(
                '<m><d><d><y>',
                array(
                    'strip_non_input_chars' => true,
                    'date_inputs' => array(
                        'm' => '<m>',
                        'd' => '<d>',
                        'y' => '<y>',
                    ),
                    'format' => '[medy]'
                )
            ),
            array(
                '[<m><d><d><y>]',
                array(
                    'strip_non_input_chars' => false,
                    'date_inputs' => array(
                        'm' => '<m>',
                        'd' => '<d>',
                        'y' => '<y>',
                    ),
                    'format' => '[medy]'
                )
            )
        );
    }
}
