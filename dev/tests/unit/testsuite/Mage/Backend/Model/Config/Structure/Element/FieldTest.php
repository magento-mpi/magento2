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

class Mage_Backend_Model_Config_Structure_Element_FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Field
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sourceFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_commentFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_depMapperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    public function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Iterator', array(), array(), '', false
        );
        $helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->any())->method('__')->will($this->returnCallback(function($arg) {
            return 'translated ' . $arg;
        }));
        $this->_factoryHelperMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);
        $this->_factoryHelperMock->expects($this->any())->method('get')->with('Mage_Module_Helper_Data')
            ->will($this->returnValue($helperMock));
        $this->_applicationMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_backendFactoryMock = $this->getMock(
            'Mage_Backend_Model_Config_BackendFactory', array(), array(), '', false
        );
        $this->_sourceFactoryMock = $this->getMock(
            'Mage_Backend_Model_Config_SourceFactory', array(), array(), '', false
        );
        $this->_commentFactoryMock = $this->getMock(
            'Mage_Backend_Model_Config_CommentFactory', array(), array(), '', false
        );
        $this->_blockFactoryMock = $this->getMock(
            'Mage_Core_Model_BlockFactory', array(), array(), '', false
        );
        $this->_depMapperMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper', array(), array(), '', false
        );

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Field(
            $this->_factoryHelperMock,
            $this->_applicationMock,
            $this->_backendFactoryMock,
            $this->_sourceFactoryMock,
            $this->_commentFactoryMock,
            $this->_blockFactoryMock,
            $this->_depMapperMock
        );
    }

    protected function tearDown()
    {
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
        unset($this->_backendFactoryMock);
        unset($this->_sourceFactoryMock);
        unset($this->_commentFactoryMock);
        unset($this->_depMapperMock);
        unset($this->_factoryHelperMock);
        unset($this->_model);
        unset($this->_blockFactoryMock);
    }

    public function testGetLabelTranslatesLabelAndPrefix()
    {
        $this->_model->setData(array('label' => 'element label', 'module' => 'Mage_Module'), 'scope');
        $this->assertEquals('translated some prefix translated element label', $this->_model->getLabel('some prefix'));
    }

    public function testGetHintTranslatesElementHint()
    {
        $this->_model->setData(array('hint' => 'element hint', 'module' => 'Mage_Module'), 'scope');
        $this->assertEquals('translated element hint', $this->_model->getHint());
    }

    public function testGetCommentTranslatesCommentTextIfNoCommentModelIsProvided()
    {
        $this->_model->setData(array('comment' => 'element comment', 'module' => 'Mage_Module'), 'scope');
        $this->assertEquals('translated element comment', $this->_model->getComment());
    }

    public function testGetCommentRetrievesCommentFromCommentModelIfItsProvided()
    {
        $config = array('comment' => array('model' => 'Model_Name'));
        $this->_model->setData($config, 'scope');
        $commentModelMock = $this->getMock('Mage_Backend_Model_Config_CommentInterface');
        $commentModelMock->expects($this->once())
            ->method('getCommentText')
            ->with('currentValue')
            ->will($this->returnValue('translatedValue'));
        $this->_commentFactoryMock->expects($this->once())
            ->method('create')
            ->with('Model_Name')
            ->will($this->returnValue($commentModelMock));
        $this->assertEquals('translatedValue', $this->_model->getComment('currentValue'));
    }

    public function testGetTooltipRetunrsTranslatedAttributeIfNoBlockIsProvided()
    {
        $this->_model->setData(array('tooltip' => 'element tooltip', 'module' => 'Mage_Module'), 'scope');
        $this->assertEquals('translated element tooltip', $this->_model->getTooltip());
    }

    public function testGetTooltipCreatesTooltipBlock()
    {
        $this->_model->setData(array('tooltip_block' => 'Mage_Core_Block_Tooltip'), 'scope');
        $tooltipBlock = $this->getMock('Mage_Core_Block');
        $tooltipBlock->expects($this->once())->method('toHtml')->will($this->returnValue('tooltip block'));
        $this->_blockFactoryMock->expects($this->once())
            ->method('createBlock')
            ->with('Mage_Core_Block_Tooltip')
            ->will($this->returnValue($tooltipBlock));
        $this->assertEquals('tooltip block', $this->_model->getTooltip());
    }

    public function testGetTypeReturnsTextByDefault()
    {
        $this->assertEquals('text', $this->_model->getType());
    }

    public function testGetTypeReturnsProvidedType()
    {
        $this->_model->setData(array('type' => 'some_type'), 'scope');
        $this->assertEquals('some_type', $this->_model->getType());
    }

    public function testGetFrontendClass()
    {
        $this->assertEquals('', $this->_model->getFrontendClass());
        $this->_model->setData(array('frontend_class' => 'some class'), 'scope');
        $this->assertEquals('some class', $this->_model->getFrontendClass());
    }

    public function testHasBackendModel()
    {
        $this->assertFalse($this->_model->hasBackendModel());
        $this->_model->setData(array('backend_model' => 'some_model'), 'scope');
        $this->assertTrue($this->_model->hasBackendModel());
    }

    public function testGetBackendModelCreatesBackendModel()
    {
        $this->_backendFactoryMock->expects($this->once())
            ->method('create')
            ->with('Mage_Model_Name')
            ->will($this->returnValue('backend_model_object'));
        $this->_model->setData(array('backend_model' => 'Mage_Model_Name'), 'scope');
        $this->assertEquals('backend_model_object', $this->_model->getBackendModel());
    }

    public function testGetSectionId()
    {
        $this->_model->setData(array('id' => 'fieldId', 'path' => 'sectionId/groupId/subgroupId'), 'scope');
        $this->assertEquals('sectionId', $this->_model->getSectionId());
    }

    public function testGetGroupPath()
    {
        $this->_model->setData(array('id' => 'fieldId', 'path' => 'sectionId/groupId/subgroupId'), 'scope');
        $this->assertEquals('sectionId/groupId/subgroupId', $this->_model->getGroupPath());
    }

    public function testGetConfigPath()
    {
        $this->_model->setData(array('config_path' => 'custom_config_path'), 'scope');
        $this->assertEquals('custom_config_path', $this->_model->getConfigPath());
    }

    public function testShowInDefault()
    {
        $this->assertFalse($this->_model->showInDefault());
        $this->_model->setData(array('showInDefault' => 1), 'scope');
        $this->assertTrue($this->_model->showInDefault());
    }

    public function testShowInWebsite()
    {
        $this->assertFalse($this->_model->showInWebsite());
        $this->_model->setData(array('showInWebsite' => 1), 'scope');
        $this->assertTrue($this->_model->showInWebsite());
    }

    public function testShowInStore()
    {
        $this->assertFalse($this->_model->showInStore());
        $this->_model->setData(array('showInStore' => 1), 'scope');
        $this->assertTrue($this->_model->showInStore());
    }

    public function testPopulateInput()
    {
        $params = array(
            'type' => 'multiselect',
            'can_be_empty' => true,
            'source_model' => 'some_model',
            'someArr' => array(
                'testVar' => 'testVal'
            )
        );
        $this->_model->setData($params, 'scope');
        $elementMock = $this->getMock('Varien_Data_Form_Element_Text', array('setOriginalData'), array(), '', false);
        unset($params['someArr']);
        $elementMock->expects($this->once())->method('setOriginalData')->with($params);
        $this->_model->populateInput($elementMock);
    }

    public function testHasValidation()
    {
        $this->assertFalse($this->_model->hasValidation());
        $this->_model->setData(array('validate' => 'validation class'), 'scope');
        $this->assertTrue($this->_model->hasValidation());
    }

    public function testCanBeEmpty()
    {
        $this->assertFalse($this->_model->canBeEmpty());
        $this->_model->setData(array('can_be_empty' => true), 'scope');
        $this->assertTrue($this->_model->canBeEmpty());
    }

    public function testHasSourceModel()
    {
        $this->assertFalse($this->_model->hasSourceModel());
        $this->_model->setData(array('source_model' => 'some_model'), 'scope');
        $this->assertTrue($this->_model->hasSourceModel());
    }

    public function testGetOptionsUsesOptionsInterfaceIfNoMethodIsProvided()
    {
        $this->_model->setData(array('source_model' => 'Source_Model_Name'), 'scope');
        $sourceModelMock = $this->getMock('Mage_Core_Model_Option_ArrayInterface');
        $this->_sourceFactoryMock->expects($this->once())
            ->method('create')
            ->with('Source_Model_Name')
            ->will($this->returnValue($sourceModelMock));
        $expected = array('testVar' => 'testVal');
        $sourceModelMock->expects($this->once())
            ->method('toOptionArray')
            ->with(false)
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->_model->getOptions());
    }

    public function testGetOptionsUsesProvidedMethodOfSourceModel()
    {
        $this->_model->setData(
            array('source_model' => 'Source_Model_Name::retrieveElements', 'path' => 'path', 'type' => 'multiselect'),
            'scope'
        );
        $sourceModelMock = $this->getMock('Varien_Object', array('setPath', 'retrieveElements'));
        $this->_sourceFactoryMock->expects($this->once())
            ->method('create')
            ->with('Source_Model_Name')
            ->will($this->returnValue($sourceModelMock));
        $expected = array('testVar1' => 'testVal1', 'testVar2' => array('subvar1' => 'subval1'));
        $sourceModelMock->expects($this->once())->method('setPath')->with('path/');
        $sourceModelMock->expects($this->once())
            ->method('retrieveElements')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->_model->getOptions());
    }

    public function testGetOptionsParsesResultOfProvidedMethodOfSourceModelIfTypeIsNotMultiselect()
    {
        $this->_model->setData(
            array('source_model' => 'Source_Model_Name::retrieveElements', 'path' => 'path', 'type' => 'select'),
            'scope'
        );
        $sourceModelMock = $this->getMock('Varien_Object', array('setPath', 'retrieveElements'));
        $this->_sourceFactoryMock->expects($this->once())
            ->method('create')
            ->with('Source_Model_Name')
            ->will($this->returnValue($sourceModelMock));
        $sourceModelMock->expects($this->once())->method('setPath')->with('path/');
        $sourceModelMock->expects($this->once())
            ->method('retrieveElements')
            ->will($this->returnValue(array(
                'var1' => 'val1',
                'var2' => array(
                    'subvar1' => 'subval1'
                )
            )));
        $expected = array(
            array('label' => 'val1', 'value' => 'var1'),
            array('subvar1' => 'subval1')
        );
        $this->assertEquals($expected, $this->_model->getOptions());
    }

    public function testGetDependenciesWithoutDependencies()
    {
        $this->_depMapperMock->expects($this->never())->method('getDependencies');
    }

    public function testGetDependenciesWithDependencies()
    {
        $fields = array(
            'field_4' => array(
                'id' => 'section_2/group_3/field_4',
                'value' => 'someValue',
                'dependPath' => array(
                    'section_2',
                    'group_3',
                    'field_4',
                ),
            ),
            'field_1' => array(
                'id' => 'section_1/group_3/field_1',
                'value' => 'someValue',
                'dependPath' => array(
                    'section_1',
                    'group_3',
                    'field_1',
                ),
            ),
        );
        $this->_model->setData(array('depends' => array('fields' => $fields)), 0);
        $this->_depMapperMock->expects($this->once())
            ->method('getDependencies')->with($fields, 'test_scope', 'test_prefix')
            ->will($this->returnArgument(0));

        $this->assertEquals($fields, $this->_model->getDependencies('test_prefix', 'test_scope'));
    }
}
