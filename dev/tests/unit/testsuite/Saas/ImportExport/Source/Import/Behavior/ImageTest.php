<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Source_Import_Behavior_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Saas_ImportExport_Model_Source_Import_Behavior_Image
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Saas_ImportExport_Helper_Data', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_ImportExport_Model_Source_Import_Behavior_Image', array(
            'helper' => $this->_helperMock,
        ));
    }

    public function testGetCode()
    {
        $this->assertEquals(Saas_ImportExport_Model_Source_Import_Behavior_Image::BEHAVIOUR_CODE_IMAGES,
            $this->_model->getCode());
    }

    public function testToArray()
    {
        $this->_helperMock->expects($this->atLeastOnce())->method('__')->with($this->isType('string'))
            ->will($this->returnArgument(0));

        $this->assertEquals(
            array(Mage_ImportExport_Model_Import::BEHAVIOR_APPEND => 'Add/Update Images'),
            $this->_model->toArray()
        );
    }
}
