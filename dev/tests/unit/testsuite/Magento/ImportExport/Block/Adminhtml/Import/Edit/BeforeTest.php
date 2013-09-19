<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Block_Adminhtml_Import_Edit_Before
 */
class Magento_ImportExport_Block_Adminhtml_Import_Edit_BeforeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test model
     *
     * @var Magento_ImportExport_Block_Adminhtml_Import_Edit_Before
     */
    protected $_model;

    /**
     * Source entity behaviors
     *
     * @var array
     */
    protected $_sourceEntities = array(
        'entity_1' => array(
            'code'  => 'behavior_1',
            'token' => 'Some_Random_First_Class',
        ),
        'entity_2' => array(
            'code'  => 'behavior_2',
            'token' => 'Some_Random_Second_Class',
        ),
    );

    /**
     * Expected entity behaviors
     *
     * @var array
     */
    protected $_expectedEntities = array(
        'entity_1' => 'behavior_1',
        'entity_2' => 'behavior_2',
    );

    /**
     * Source unique behaviors
     *
     * @var array
     */
    protected $_sourceBehaviors = array(
        'behavior_1' => 'Some_Random_First_Class',
        'behavior_2' => 'Some_Random_Second_Class',
    );

    /**
     * Expected unique behaviors
     *
     * @var array
     */
    protected $_expectedBehaviors = array('behavior_1', 'behavior_2');

    protected function setUp()
    {
        $coreHelper = $this->getMock('Magento_Core_Helper_Data', array('jsonEncode'), array(), '', false, false);
        $coreHelper->expects($this->any())
            ->method('jsonEncode')
            ->will($this->returnCallback(array($this, 'jsonEncodeCallback')));

        $importModel = $this->getMock(
            'Magento_ImportExport_Model_Import',
            array('getEntityBehaviors', 'getUniqueEntityBehaviors'),
            array(),
            '',
            false
        );
        $importModel->expects($this->any())
            ->method('getEntityBehaviors')
            ->will($this->returnValue($this->_sourceEntities));
        $importModel->expects($this->any())
            ->method('getUniqueEntityBehaviors')
            ->will($this->returnValue($this->_sourceBehaviors));

        $arguments = array(
            'coreData'  => $coreHelper,
            'importModel' => $importModel,
            'urlBuilder' => $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false)
        );
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_ImportExport_Block_Adminhtml_Import_Edit_Before',
            $arguments
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Callback method for Magento_Core_Helper_Data::jsonEncode
     *
     * @param mixed $data
     * @return string
     */
    public function jsonEncodeCallback($data)
    {
        return Zend_Json::encode($data);
    }

    /**
     * Test for getEntityBehaviors method
     *
     * @covers Magento_ImportExport_Block_Adminhtml_Import_Edit_Before::getEntityBehaviors
     */
    public function testGetEntityBehaviors()
    {
        $actualEntities = $this->_model->getEntityBehaviors();
        $expectedEntities = Zend_Json::encode($this->_expectedEntities);
        $this->assertEquals($expectedEntities, $actualEntities);
    }

    /**
     * Test for getUniqueBehaviors method
     *
     * @covers Magento_ImportExport_Block_Adminhtml_Import_Edit_Before::getUniqueBehaviors
     */
    public function testGetUniqueBehaviors()
    {
        $actualBehaviors = $this->_model->getUniqueBehaviors();
        $expectedBehaviors = Zend_Json::encode($this->_expectedBehaviors);
        $this->assertEquals($expectedBehaviors, $actualBehaviors);
    }
}
