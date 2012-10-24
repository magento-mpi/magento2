<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_StructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    public function setUp()
    {
        $filePath = dirname(__DIR__) . '/_files';

        $this->_appMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_converterMock = $this->getMock('Mage_Backend_Model_Config_Structure_Converter');
        $this->_converterMock->expects($this->once())->method('convert')->will($this->returnValue(array(
            'config' => array(
                'system' => array(
                    'tabs' => array(
                        'tab_1' => array('id' => 'tab_1', 'label' => 'Tab 1'),
                        'tab_2' => array('id' => 'tab_2', 'label' => 'Tab 2')
                    ),
                    'sections' => array(
                        'section_1' => array('id' => 'section_1', 'label' => 'Section 1'),
                        'section_2' => array('id' => 'section_2', 'label' => 'Section 2')
                    )
                )
            )
        )));

        $this->_model = new Mage_Backend_Model_Config_Structure(array(
            'sourceFiles' => array(
                $filePath . '/system_1.xml',
                $filePath . '/system_2.xml'
            ),
            'app' => $this->_appMock,
            'converter' => $this->_converterMock
        ));
    }

    public function testGetSectionsReturnsAllSections()
    {

        $sections = $this->_model->getSections();
        $this->assertCount(2, $sections);
        $section = reset($sections);
        $this->assertEquals('section_1', $section['id']);
        $section = next($sections);
        $this->assertEquals('section_2', $section['id']);
    }

    public function testGetSectionReturnsSectionByKey()
    {
        $section = $this->_model->getSection('section_1');
        $this->assertEquals('section_1', $section['id']);
        $section = $this->_model->getSection(null, 'section_1');
        $this->assertEquals('section_1', $section['id']);
        $section = $this->_model->getSection(null, null, 'section_1');
        $this->assertEquals('section_1', $section['id']);
    }

    public function testHasChildrenReturnsFalseIfNodeCannotBeShown()
    {
        $this->assertFalse($this->_model->hasChildren(array()));
    }

    public function testHasChildrenReturnsTrueIfNodeIsField()
    {
        $this->_appMock->expects($this->any())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertTrue($this->_model->hasChildren(array()));
    }

    public function testHasChildrenReturnsFalseForEmptySection()
    {
        $this->_appMock->expects($this->any())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->hasChildren(array('groups' => array())));
    }

    public function testHasChildrenReturnsFalseForEmptyGroup()
    {
        $this->_appMock->expects($this->any())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->hasChildren(array('fields' => array())));
    }
}
