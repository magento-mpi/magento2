<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test number of days after which layout will be removed
     */
    const TEST_DAYS_TO_EXPIRE = 5;

    /**
     * @var Mage_DesignEditor_Model_Observer
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @return array
     */
    public function setThemeDataProvider()
    {
        return array(
            'no theme id'      => array('$themeId' => null),
            'correct theme id' => array('$themeId' => 1),
        );
    }

    public function testSaveQuickStyles()
    {
        $generatedContent = 'generated css content';

        $cacheManager = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager');
        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array(), array(), '', false);

        /** Prepare renderer */
        $renderer = $this->getMock(
            'Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer', array('render'), array(), '', false
        );

        $renderer->expects($this->once())
            ->method('render')
            ->will($this->returnValue($generatedContent));

        /** Prepare CSS */
        $cssFile = $this->getMock(
            'Mage_Core_Model_Theme_Customization_File_Css', array('setDataForSave'), array(), '', false
        );
        $cssFile->expects($this->once())
            ->method('setDataForSave')
            ->with(array(Mage_Core_Model_Theme_Customization_File_Css::QUICK_STYLE_CSS => $generatedContent))
            ->will($this->returnValue($renderer));

        /** Prepare theme */
        $theme = $this->getMock('Mage_Core_Model_Theme', array('setCustomization', 'save'), array(), '', false);

        $theme->expects($this->once())
            ->method('setCustomization')
            ->with($cssFile)
            ->will($this->returnValue($theme));

        $theme->expects($this->once())
            ->method('save')
            ->will($this->returnValue($theme));

        /** Prepare configuration */
        $configuration = $this->getMock(
            'Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration',
            array('getControlConfig', 'getTheme'), array(), '', false
        );
        $quickStyle = $this->getMock(
            'Mage_DesignEditor_Model_Config_Control_QuickStyles', array(), array(), '', false
        );

        $configuration->expects($this->once())
            ->method('getControlConfig')
            ->will($this->returnValue($quickStyle));

        /** Prepare event */
        $event = $this->getMock('Varien_Event_Observer', array('getData'), array(), '', false);

        $event->expects($this->at(0))
            ->method('getData')
            ->with('configuration')
            ->will($this->returnValue($configuration));

        $event->expects($this->at(1))
            ->method('getData')
            ->with('theme')
            ->will($this->returnValue($theme));

        /** Prepare observer */
        $objectManager->expects($this->at(0))
            ->method('create')
            ->with('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer')
            ->will($this->returnValue($renderer));

        $objectManager->expects($this->at(1))
            ->method('create')
            ->with('Mage_Core_Model_Theme_Customization_File_Css')
            ->will($this->returnValue($cssFile));

        $this->_model = new Mage_DesignEditor_Model_Observer($objectManager, $helper, $cacheManager);
        $this->_model->saveQuickStyles($event);
    }
}
