<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/Abstract.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/Group.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Mapper/Section.php';

/**
 * Test case for Magento_Tools_Migration_System_Configuration_Mapper_Section
 */
class Magento_Test_Tools_Migration_System_Configuration_Mapper_SectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_groupMapperMock;

    /**
     * @var Magento_Tools_Migration_System_Configuration_Mapper_Section
     */
    protected $_object;

    protected function setUp()
    {
        $this->_groupMapperMock = $this->getMock('Magento_Tools_Migration_System_Configuration_Mapper_Group',
            array(), array(), '', false
        );

        $this->_object = new Magento_Tools_Migration_System_Configuration_Mapper_Section($this->_groupMapperMock);
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
                'tab' => array('#text' => 'some tab'),
            ),
            'section_2' => array(),
            'section_3' => array(
                'groups' => array(
                    'label' => 'label'
                )
            ),
        );

        $expected = array(
            array(
                'nodeName' => 'section',
                '@attributes' => array(
                    'id' => 'section_1',
                    'sortOrder' => 10,
                    'type' => 'text',
                ),
                'parameters' => array(
                    array(
                        'name' => 'class',
                        '#text' => 'css class'
                    ),
                    array(
                        'name' => 'label',
                        '#text' => 'section label'
                    ),
                    array(
                        'name' => 'comment',
                        '#cdata-section' => 'section comment'
                    ),
                    array(
                        'name' => 'resource',
                        '#text' => 'acl'
                    ),
                    array(
                        'name' => 'header_css',
                        '#text' => 'some css class'
                    ),
                    array(
                        'name' => 'tab',
                        '#text' => 'some tab'
                    ),
                )
            ),
            array(
                'nodeName' => 'section',
                '@attributes' => array(
                    'id' => 'section_2',
                ),
                'parameters' => array ()
            ),
            array(
                'nodeName' => 'section',
                '@attributes' => array(
                    'id' => 'section_3',
                ),
                'parameters' => array(),
                'subConfig' => array(
                    'label' => 'label'
                )
            )
        );

        $this->_groupMapperMock->expects($this->once())
            ->method('transform')->with(array('label' => 'label'))->will($this->returnArgument(0));

        $actual = $this->_object->transform($config);
        $this->assertEquals($expected, $actual);
    }
}
