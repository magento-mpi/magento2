<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper/Abstract.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper/Tab.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper/Section.php';

/**
 * Test case for Magento_Tools_Migration_System_Configuration_Mapper
 */
class Magento_Test_Tools_Migration_System_Configuration_MapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tools_Migration_System_Configuration_Mapper
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_tabMapperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionMapperMock;

    protected function setUp()
    {
        $this->_tabMapperMock = $this->getMock('Magento_Tools_Migration_System_Configuration_Mapper_Tab',
            array(), array(), '', false
        );
        $this->_sectionMapperMock = $this->getMock('Magento_Tools_Migration_System_Configuration_Mapper_Section',
            array(), array(), '', false
        );

        $this->_object = new Magento_Tools_Migration_System_Configuration_Mapper(
            $this->_tabMapperMock,
            $this->_sectionMapperMock
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_tabMapperMock = null;
        $this->_sectionMapperMock = null;
    }

    public function testTransformWithSetTabsAndSections()
    {
        $config = array(
            'comment' => 'test comment',
            'tabs' => array(
                'test tabs config',
            ),
            'sections' => array(
                'test sections config',
            ),
        );

        $this->_tabMapperMock->expects($this->once())->method('transform')
            ->with(array('test tabs config'))->will($this->returnArgument(0));

        $this->_sectionMapperMock->expects($this->once())->method('transform')
            ->with(array('test sections config'))->will($this->returnArgument(0));

        $expected = array(
            'comment' => 'test comment',
            'nodes' => array(
                'test tabs config',
                'test sections config',
            ),
        );
        $actual = $this->_object->transform($config);

        $this->assertEquals($expected, $actual);
    }

    public function testTransformWithoutSetTabsAndSections()
    {
        $config = array(
            'comment' => 'test comment',
        );

        $this->_tabMapperMock->expects($this->once())->method('transform')
            ->with(array())->will($this->returnArgument(0));

        $this->_sectionMapperMock->expects($this->once())->method('transform')
            ->with(array())->will($this->returnArgument(0));

        $expected = array(
            'comment' => 'test comment',
            'nodes' => array(),
        );
        $actual = $this->_object->transform($config);

        $this->assertEquals($expected, $actual);
    }
}
