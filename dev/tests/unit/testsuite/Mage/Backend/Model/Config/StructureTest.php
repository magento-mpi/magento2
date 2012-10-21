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
                        array('id' => 'tab_1', 'label' => 'Tab 1'),
                        array('id' => 'tab_2', 'label' => 'Tab 2')
                    ),
                    'sections' => array(
                        array('id' => 'section_1', 'label' => 'Section 1'),
                        array('id' => 'section_2', 'label' => 'Section 2')
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
        $this->assertEquals('section_1', $sections[0]['id']);
        $this->assertEquals('section_2', $sections[1]['id']);
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



    /**
     * @param Varien_Simplexml_Element $xmlData
     * @param boolean $isSingleStoreMode
     * @param Varien_Simplexml_Element $node
     * @param string $website
     * @param string $store
     * @param mixed $expectedResult
     * @param string $message
     * @dataProvider addItemFilterDataProvider
     */
/*    public function testHasChildren($xmlData, $isSingleStoreMode, $node, $website, $store, $expectedResult, $message)
    {
        $app = $this->getMock('Mage_Core_Model_App', array('isSingleStoreMode'), array(), '', true);
        $app->expects($this->any())
            ->method('isSingleStoreMode')
            ->will($this->returnValue($isSingleStoreMode));

        $config = new Mage_Backend_Model_Config_Structure(array(
            'data' => $xmlData,
            'app' => $app,
        ));
        $result = $config->hasChildren($node, $website, $store);
        $this->assertEquals($expectedResult, $result, $message);
    }

    public function addItemFilterDataProvider()
    {
        $data = file_get_contents(__DIR__ . '/_files/system.xml');
        $xmlData = new Varien_Simplexml_Element($data);
        return array(
            array($xmlData, false, $xmlData->sections->dev, null, null, true, 'Case 1'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, null, null, true, 'Case 2'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, 'base', null, true, 'Case 3'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, 'base', 'default', true, 'Case 4'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, null, null, false, 'Case 5'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, 'base', null, true, 'Case 6'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, 'base', 'default', true, 'Case 7'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, null, null, false, 'Case 8'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, 'base', null, false, 'Case 9'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, 'base', 'default', true, 'Case 10'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, null, null, true, 'Case 11'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, 'base', null, true, 'Case 12'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, 'base', 'default', true, 'Case 13'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, null, null, true, 'Case 14'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, 'base', null, true, 'Case 15'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, 'base', 'default', true, 'Case 16'),
            array($xmlData, true, $xmlData->sections->dev->groups->price, null, null, false, 'Case 17'),
            array($xmlData, true, $xmlData->sections->dev->groups->price, 'base', null, false, 'Case 17'),
            array($xmlData, true, $xmlData->sections->dev->groups->price, 'base', 'default', false, 'Case 17'),
        );
    }*/
}
