<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\System\Configuration\Mapper;


require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/AbstractMapper.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/Group.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/Section.php';
/**
 * Test case for \Magento\Tools\Migration\System\Configuration\Mapper\Section
 */
class SectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_groupMapperMock;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Mapper\Section
     */
    protected $_object;

    protected function setUp()
    {
        $this->_groupMapperMock = $this->getMock(
            'Magento\Tools\Migration\System\Configuration\Mapper\Group',
            array(),
            array(),
            '',
            false
        );

        $this->_object = new \Magento\Tools\Migration\System\Configuration\Mapper\Section($this->_groupMapperMock);
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_groupMapperMock = null;
    }

    public function testTransform()
    {
        $config = array(
            'section_1' => array(
                'sort_order' => array('#text' => 10),
                'frontend_type' => array('#text' => 'text'),
                'class' => array('#text' => 'css class'),
                'label' => array('#text' => 'section label'),
                'comment' => array('#cdata-section' => 'section comment'),
                'resource' => array('#text' => 'acl'),
                'header_css' => array('#text' => 'some css class'),
                'tab' => array('#text' => 'some tab')
            ),
            'section_2' => array(),
            'section_3' => array('groups' => array('label' => 'label'))
        );

        $expected = array(
            array(
                'nodeName' => 'section',
                '@attributes' => array('id' => 'section_1', 'sortOrder' => 10, 'type' => 'text'),
                'parameters' => array(
                    array('name' => 'class', '#text' => 'css class'),
                    array('name' => 'label', '#text' => 'section label'),
                    array('name' => 'comment', '#cdata-section' => 'section comment'),
                    array('name' => 'resource', '#text' => 'acl'),
                    array('name' => 'header_css', '#text' => 'some css class'),
                    array('name' => 'tab', '#text' => 'some tab')
                )
            ),
            array('nodeName' => 'section', '@attributes' => array('id' => 'section_2'), 'parameters' => array()),
            array(
                'nodeName' => 'section',
                '@attributes' => array('id' => 'section_3'),
                'parameters' => array(),
                'subConfig' => array('label' => 'label')
            )
        );

        $this->_groupMapperMock->expects(
            $this->once()
        )->method(
            'transform'
        )->with(
            array('label' => 'label')
        )->will(
            $this->returnArgument(0)
        );

        $actual = $this->_object->transform($config);
        $this->assertEquals($expected, $actual);
    }
}
