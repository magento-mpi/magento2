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
            'Trailing slash with section only' => array(
                array('o/'),
                '\'o/\' is incorrect path'
            ),
            'Trailing slash with section/group only' => array(
                array('o/t/'),
                '\'o/t/\' is incorrect path'
            ),
            'Trailing slash with full path' => array(
                array('o/t/t/'),
                '\'o/t/t/\' is incorrect path'
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage 'section/group' is incorrect section path
     */
    public function testIsSectionDisabledChunkLengthException()
    {
        $this->_model->isSectionDisabled('section/group');
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsSectionDisabledIncorrectPathException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        $this->_model->isSectionDisabled($disabledList[0]);
    }

    public function testIsSectionDisabled()
    {
        $this->assertTrue($this->_model->isSectionDisabled('o'));
        $this->assertFalse($this->_model->isSectionDisabled('wrong_section'));
    }

    /**
     * @dataProvider isGroupDisabledChunkLengthExceptionDataProvider
     */
    public function testIsGroupDisabledChunkLengthException($path, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);
        $this->_model->isGroupDisabled($path);
    }

    public function isGroupDisabledChunkLengthExceptionDataProvider()
    {
        return array(
            'field exists' => array('section/group/field', "'section/group/field' is incorrect group path"),
            'group does not exist' => array('section', "'section' is incorrect group path")
        );
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsGroupDisabledIncorrectPathException($disabledList, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);
        $this->_model->isGroupDisabled($disabledList[0]);
    }

    /**
     * @dataProvider isGroupDisabledInListDataProvider
     */
    public function testIsGroupDisabled($groupPath, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->isGroupDisabled($groupPath));
    }

    public function isGroupDisabledInListDataProvider()
    {
        return array(
            'group in list' => array('oo/tt', true),
            'section in list' => array('o/ttt', true),
            'not in list with the same section part' => array('oo/wrong_group', false),
            'not in list with the same group part' => array('wrong_section/tt', false),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage 'section/group' is incorrect field path
     */
    public function testIsFieldDisabledChunkLengthException()
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
            'not in list with the same section/group part' => array('ooo/ttt/wrong_part', false),
            'not in list' => array('not/in/list', false),
            'not in list with the same field part' => array('not/in/ttt', false),
            'not in list with the same group/field part' => array('not/in/list', false),
        );
    }

    public function testGetPlainList()
    {
        $list = Saas_Saas_Model_DisabledConfiguration_Config::getPlainList();
        $this->assertInternalType('array', $list);
        $this->assertNotEmpty($list);
    }
}
