<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplateFile()
    {
        $design = $this->getMock('Mage_Core_Model_Design_Package', array('getFilename'), array(), '', false);
        $template = 'fixture';
        $area = 'areaFixture';
        $block = new Mage_Core_Block_Template(
            $this->getMock('Mage_Core_Controller_Request_Http'),
            $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Event_Manager'),
            $this->getMock('Mage_Core_Model_Url'),
            $this->getMock('Mage_Core_Model_Translate', array(), array($design)),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $design,
            $this->getMock('Mage_Core_Model_Session'),
            $this->getMock('Mage_Core_Model_Store_Config'),
            $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Factory_Helper'),
            $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false),
            array('template' => $template, 'area' => $area)
        );

        $params = array('module' => 'Mage_Core', 'area' => $area);
        $design->expects($this->once())->method('getFilename')->with($template, $params);
        $block->getTemplateFile();
    }

    /**
     * @param string $filename
     * @param string $expectedOutput
     * @dataProvider fetchViewDataProvider
     */
    public function testFetchView($filename, $expectedOutput)
    {
        $layout = $this->getMock('Mage_Core_Model_Layout', array('isDirectOutput'), array(), '', false);
        $design = $this->getMock('Mage_Core_Model_Design_Package');
        $block = $this->getMock('Mage_Core_Block_Template', array('getShowTemplateHints'), array(
            $this->getMock('Mage_Core_Controller_Request_Http'),
            $layout,
            $this->getMock('Mage_Core_Model_Event_Manager'),
            $this->getMock('Mage_Core_Model_Url'),
            $this->getMock('Mage_Core_Model_Translate', array(), array($design)),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Session'),
            $this->getMock('Mage_Core_Model_Store_Config'),
            $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Factory_Helper'),
            new Mage_Core_Model_Dir(
                __DIR__ . '/_files',
                array(Mage_Core_Model_Dir::APP => ''),
                array(Mage_Core_Model_Dir::APP => __DIR__)
            ),
            $this->getMock('Mage_Core_Model_Logger', array('log'), array(), '', false)
        ));
        $layout->expects($this->once())->method('isDirectOutput')->will($this->returnValue(false));

        $this->assertSame($block, $block->assign(array('varOne' => 'value1', 'varTwo' => 'value2')));
        $this->assertEquals($expectedOutput, $block->fetchView(__DIR__ . "/_files/{$filename}"));
    }

    /**
     * @return array
     */
    public function fetchViewDataProvider()
    {
        return array(
            array('template_test_assign.phtml', 'value1, value2'),
            array('invalid_file', ''),
        );
    }
}
