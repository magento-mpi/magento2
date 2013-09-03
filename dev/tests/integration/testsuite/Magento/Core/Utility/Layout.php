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
class Magento_Core_Utility_Layout
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
     * @return Magento_Core_Model_Layout_Merge
     */
    public function getLayoutUpdateFromFixture($layoutUpdatesFile)
    {
        $objectManager = Mage::getObjectManager();
        /** @var Magento_Core_Model_Layout_File_Factory $fileFactory */
        $fileFactory = $objectManager->get('Magento_Core_Model_Layout_File_Factory');
        $file = $fileFactory->create($layoutUpdatesFile, 'Magento_Core');
        $fileSource = $this->_testCase->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $fileSource->expects(PHPUnit_Framework_TestCase::any())
            ->method('getFiles')
            ->will(PHPUnit_Framework_TestCase::returnValue(array($file)));
        $cache = $this->_testCase->getMockForAbstractClass('\Magento\Cache\FrontendInterface');
        return $objectManager->create(
            'Magento_Core_Model_Layout_Merge', array('fileSource' => $fileSource, 'cache' => $cache)
        );
    }

    /**
     * Retrieve new layout model instance with layout updates from a fixture file
     *
     * @param string $layoutUpdatesFile
     * @param array $args
     * @return Magento_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutFromFixture($layoutUpdatesFile, array $args = array())
    {
        $layout = $this->_testCase->getMock('Magento_Core_Model_Layout', array('getUpdate'), $args);
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
            'design'             => Mage::getObjectManager()->get('Magento_Core_Model_View_DesignInterface'),
            'blockFactory'       => Mage::getObjectManager()->create('Magento_Core_Model_BlockFactory', array()),
            'structure'          => Mage::getObjectManager()->create('Magento\Data\Structure', array()),
            'argumentProcessor'  => Mage::getObjectManager()->create('Magento_Core_Model_Layout_Argument_Processor',
                array()
            ),
            'scheduledStructure' => Mage::getObjectManager()->create('Magento_Core_Model_Layout_ScheduledStructure',
                array()
            ),
            'dataServiceGraph'   => Mage::getObjectManager()->create('Magento_Core_Model_DataService_Graph', array()),
        );
    }
}
