<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core layout utility
 */
class Mage_Core_Utility_Layout
{
    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_testCase;

    public function __construct(PHPUnit_Framework_TestCase $testCase)
    {
        $this->_testCase = $testCase;
    }

    /**
     * Retrieve new layout update model instance with XML data from a fixture file
     *
     * @param string $layoutUpdatesFile
     * @return Mage_Core_Model_Layout_Merge|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutUpdateFromFixture($layoutUpdatesFile)
    {
        $layoutUpdate = $this->_testCase->getMock(
            'Mage_Core_Model_Layout_Merge',
            array('getFileLayoutUpdatesXml'),
            array(
<<<<<<< HEAD
                Mage::getObjectManager()->get('Mage_Core_Model_Design_PackageInterface'),
                Mage::getObjectManager()->get('Mage_Core_Model_StoreManagerInterface'),
                $this->_testCase->getMockForAbstractClass('Mage_Core_Model_Layout_File_SourceInterface'),
=======
                Mage::getObjectManager()->get('Mage_Core_Model_View_Design'),
                Mage::getObjectManager()->get('Mage_Core_Model_View_FileSystem'),
>>>>>>> origin/master
                $this->_testCase->getMockForAbstractClass('Magento_Cache_FrontendInterface'),
            )
        );

        $reflector = new ReflectionProperty(get_class($layoutUpdate), '_elementClass');
        $reflector->setAccessible(true);
        $layoutUpdatesXml = simplexml_load_file($layoutUpdatesFile, $reflector->getValue($layoutUpdate));
        $layoutUpdate->expects(PHPUnit_Framework_TestCase::any())
            ->method('getFileLayoutUpdatesXml')
            ->will(PHPUnit_Framework_TestCase::returnValue($layoutUpdatesXml));
        return $layoutUpdate;
    }

    /**
     * Retrieve new layout model instance with layout updates from a fixture file
     *
     * @param string $layoutUpdatesFile
     * @param array $args
     * @return Mage_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutFromFixture($layoutUpdatesFile, array $args = array())
    {
        $layout = $this->_testCase->getMock('Mage_Core_Model_Layout', array('getUpdate'), $args);
        $layoutUpdate = $this->getLayoutUpdateFromFixture($layoutUpdatesFile);
        $layoutUpdate->asSimplexml();
        $layout->expects(PHPUnit_Framework_TestCase::any())
            ->method('getUpdate')
            ->will(PHPUnit_Framework_TestCase::returnValue($layoutUpdate));
        return $layout;
    }

    /**
     * Retrieve object that will be used for layout instantiation
     *
     * @return array
     */
    public function getLayoutDependencies()
    {
        return array(
            'design'             => Mage::getObjectManager()->get('Mage_Core_Model_Design_PackageInterface'),
            'blockFactory'       => Mage::getObjectManager()->create('Mage_Core_Model_BlockFactory', array()),
            'structure'          => Mage::getObjectManager()->create('Magento_Data_Structure', array()),
            'argumentProcessor'  => Mage::getObjectManager()->create('Mage_Core_Model_Layout_Argument_Processor',
                array()
            ),
            'translator'         => Mage::getObjectManager()->create('Mage_Core_Model_Layout_Translator', array()),
            'scheduledStructure' => Mage::getObjectManager()->create('Mage_Core_Model_Layout_ScheduledStructure',
                array()
            ),
            'dataServiceGraph'   => Mage::getObjectManager()->create('Mage_Core_Model_DataService_Graph', array()),
        );
    }
}
