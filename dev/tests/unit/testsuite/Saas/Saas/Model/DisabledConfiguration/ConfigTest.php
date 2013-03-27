<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manages disabled fields/groups/sections in system configuration
 */
class Saas_Saas_Model_DisabledConfiguration_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Saas_Model_DisabledConfiguration_Config
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_disabledList = array(
        'o',
        'oo/tt',
        'oo/tt_',
        'ooo/ttt/ttt',
        'ooo/ttt_/ttt_',
    );

    public function setUp()
    {
        $this->_model = new Saas_Saas_Model_DisabledConfiguration_Config($this->_disabledList);
    }

    public function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testConstructorException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        new Saas_Saas_Model_DisabledConfiguration_Config($disabledList);
    }

    public function incorrectPathExceptionDataProvider()
    {
        return array(
            'Empty path' => array(
                array(''),
                '\'\' is incorrect path'
            ),
            'Incorrect characters in path' => array(
                array('(bad characters'),
                '\'(bad characters\' is incorrect path'
            ),
            'Some valid xpath chars are incorrect' => array(
                array('//title[@lang]'),
                '\'//title[@lang]\' is incorrect path'
            ),
            'Too many chunks in xpath' => array(
                array('o/t/t/f'),
                '\'o/t/t/f\' is incorrect path'
            ),
            'Some chunks are empty' => array(
                array('o//t'),
                '\'o//t\' is incorrect path'
            ),
        );
    }

    public function testConstructor()
    {
        $this->assertSame($this->_disabledList, $this->_model->getDisabledPaths());

        $varSections = new ReflectionProperty($this->_model, '_sections');
        $varSections->setAccessible(true);
        $this->assertSame(array('o' => 'o'), $varSections->getValue($this->_model));

        $varGroups = new ReflectionProperty($this->_model, '_groups');
        $varGroups->setAccessible(true);
        $this->assertSame(array('oo/tt' => 'oo/tt', 'oo/tt_' => 'oo/tt_'), $varGroups->getValue($this->_model));

        $varFields = new ReflectionProperty($this->_model, '_fields');
        $varFields->setAccessible(true);
        $this->assertSame(
            array('ooo/ttt/ttt' => 'ooo/ttt/ttt', 'ooo/ttt_/ttt_' => 'ooo/ttt_/ttt_'),
            $varFields->getValue($this->_model)
        );
    }

    /**
     * @expectedException LengthException
     * @expectedExceptionMessage 'section/group' is incorrect section path
     */
    public function testIsSectionDisabledLengthException()
    {
        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->_model->isSectionDisabled('section/group', $configStructure);
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsSectionDisabledIncorrectPathException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->_model->isSectionDisabled($disabledList[0], $configStructure);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage 'section' should extend Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
     */
    public function testIsSectionDisabledLogicException()
    {
        $simpleConfigElement =
            $this->getMock('Mage_Backend_Model_Config_Structure_ElementAbstract', array(), array(), '', false);
        $configStructure =
            $this->getMock('Mage_Backend_Model_Config_Structure', array('getElement'), array(), '', false);
        $configStructure->expects($this->once())
            ->method('getElement')
            ->with('section')
            ->will($this->returnValue($simpleConfigElement));
        $this->_model->isSectionDisabled('section', $configStructure);
    }

    public function testIsSectionDisabledInList()
    {
        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->assertTrue($this->_model->isSectionDisabled('o', $configStructure));
    }

    /**
     * @dataProvider isSectionDisabledGeneralDataProvider
     */
    public function testIsSectionDisabledGeneral($isGroupDisabled, $hasChildren, $expectedResult)
    {
        $groupConfigElement =
            $this->getMock('Mage_Backend_Model_Config_Structure_Element_Group', array('getPath'), array(), '', false);
        $groupConfigElement->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('section/group'));

        $compositeConfig = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Section',
            array('getChildren', 'hasChildren'), array(), '', false
        );
        $compositeConfig->expects($this->any())
            ->method('hasChildren')
            ->will($this->returnValue($hasChildren));

        $compositeConfig->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($groupConfigElement)));

        $configStructure =
            $this->getMock('Mage_Backend_Model_Config_Structure', array('getElement'), array(), '', false);
        $configStructure->expects($this->once())
            ->method('getElement')
            ->with('section')
            ->will($this->returnValue($compositeConfig));

        $model = $this->getMock(
            'Saas_Saas_Model_DisabledConfiguration_Config',
            array('isGroupDisabled'),
            array($this->_disabledList)
        );
        $model->expects($this->any())
            ->method('isGroupDisabled')
            ->will($this->returnValue($isGroupDisabled));

        $this->assertEquals($expectedResult, $model->isSectionDisabled('section', $configStructure));
    }

    public function isSectionDisabledGeneralDataProvider()
    {
        return array(
            'has children, children are disabled' => array(true, true, true),
            'has children, children are not disabled' => array(false, true, false),
            'doesn\'t have children' => array(true, false, false),
        );
    }

    /**
     * @expectedException LengthException
     * @expectedExceptionMessage 'section/group/field' is incorrect group path
     */
    public function testIsGroupDisabledLengthException()
    {
        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->_model->isGroupDisabled('section/group/field', $configStructure);
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsGroupDisabledIncorrectPathException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->_model->isGroupDisabled($disabledList[0], $configStructure);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage 's/g' should extend Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
     */
    public function testIsGroupDisabledLogicException()
    {
        $simpleConfigElement =
            $this->getMock('Mage_Backend_Model_Config_Structure_ElementAbstract', array(), array(), '', false);
        $configStructure =
            $this->getMock('Mage_Backend_Model_Config_Structure', array('getElement'), array(), '', false);
        $configStructure->expects($this->once())
            ->method('getElement')
            ->with('s/g')
            ->will($this->returnValue($simpleConfigElement));
        $this->_model->isGroupDisabled('s/g', $configStructure);
    }

    /**
     * @dataProvider isGroupDisabledInListDataProvider
     */
    public function testIsGroupDisabledInList($groupPath)
    {
        $configStructure = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->assertTrue($this->_model->isGroupDisabled($groupPath, $configStructure));
    }

    public function isGroupDisabledInListDataProvider()
    {
        return array(
            'group in list' => array('oo/tt'),
            'section in list' => array('o/ttt')
        );
    }

    /**
     * @dataProvider isGroupDisabledGeneralDataProvider
     */
    public function testIsGroupDisabledGeneral($isFieldDisabled, $hasChildren, $expectedResult)
    {
        $groupConfigElement =
            $this->getMock('Mage_Backend_Model_Config_Structure_Element_Field', array('getPath'), array(), '', false);
        $groupConfigElement->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('section/group/field'));

        $compositeConfig = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Section',
            array('getChildren', 'hasChildren'), array(), '', false
        );

        $compositeConfig->expects($this->any())
            ->method('hasChildren')
            ->will($this->returnValue($hasChildren));

        $compositeConfig->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($groupConfigElement)));

        $configStructure =
            $this->getMock('Mage_Backend_Model_Config_Structure', array('getElement'), array(), '', false);
        $configStructure->expects($this->once())
            ->method('getElement')
            ->with('section/group')
            ->will($this->returnValue($compositeConfig));

        $model = $this->getMock(
            'Saas_Saas_Model_DisabledConfiguration_Config',
            array('isFieldDisabled'),
            array($this->_disabledList)
        );
        $model->expects($this->any())
            ->method('isFieldDisabled')
            ->will($this->returnValue($isFieldDisabled));

        $this->assertEquals($expectedResult, $model->isGroupDisabled('section/group', $configStructure));
    }

    public function isGroupDisabledGeneralDataProvider()
    {
        return array(
            'has children, children are disabled' => array(true, true, true),
            'has children, children are not disabled' => array(false, true, false),
            'doesn\'t have children' => array(false, false, false),
        );
    }

    /**
     * @expectedException LengthException
     * @expectedExceptionMessage 'section/group' is incorrect field path
     */
    public function testIsFieldDisabledLengthException()
    {
        $this->_model->isFieldDisabled('section/group');
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsFieldDisabledIncorrectPathException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        $this->_model->isFieldDisabled($disabledList[0]);
    }

    /**
     * @dataProvider isFieldDisabledDataProvider
     */
    public function testIsFieldDisabled($path, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->isFieldDisabled($path));
    }

    public function isFieldDisabledDataProvider()
    {
        return array(
            'field in list' => array('ooo/ttt/ttt', true),
            'group in list' => array('oo/tt_/not_important', true),
            'section in list' => array('o/not_important/not_important', true),
            'not in list' => array('not/in/list', false),
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage File with disabled configuration options was not found
     */
    public function testGetDisabledConfigurationException()
    {
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        Saas_Saas_Model_DisabledConfiguration_Config::getDisabledConfiguration($filesystem);
    }
}
