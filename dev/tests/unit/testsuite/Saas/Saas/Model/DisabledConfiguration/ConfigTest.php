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
    public function testConstructorException($path, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        new Saas_Saas_Model_DisabledConfiguration_Config(array($path));
    }

    /**
     * @dataProvider incorrectPathExceptionDataProvider
     */
    public function testIsPathDisabledException($path, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);

        $model = new Saas_Saas_Model_DisabledConfiguration_Config(array());
        $model->isPathDisabled($path);
    }

    public function incorrectPathExceptionDataProvider()
    {
        return array(
            'Empty path' => array(
                '',
                '\'\' is incorrect path'
            ),
            'Incorrect characters in path' => array(
                '(bad characters',
                '\'(bad characters\' is incorrect path'
            ),
            'Some valid xpath chars are incorrect' => array(
                '//title[@lang]',
                '\'//title[@lang]\' is incorrect path'
            ),
            'Some chunks are empty' => array(
                'o//t',
                '\'o//t\' is incorrect path'
            ),
            'Trailing slash' => array(
                'o/t/',
                '\'o/t/\' is incorrect path'
            ),
        );
    }


    /**
     * @dataProvider isPathDisabledDataProvider
     */
    public function testIsPathDisabled($path, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->isPathDisabled($path));
    }

    public function isPathDisabledDataProvider()
    {
        return array(
            'section in list' => array('o', true),
            'section not in list' => array('not_in_list', false),
            'group in list' => array('oo/tt_', true),
            'group with section in list' => array('o/not_in_list', true),
            'group not in list' => array('oo/not_in_list', false),
            'group not in list with same group part' => array('not_in_list/tt_', false),
            'field in list' => array('ooo/ttt/ttt', true),
            'field with group in list' => array('oo/tt_/not_important', true),
            'field with section in list' => array('o/not_important/not_important', true),
            'field not in list with the same section/group part' => array('ooo/ttt/wrong_part', false),
            'field not in list' => array('not/in/list', false),
            'field not in list with the same field part' => array('not/in/ttt', false),
            'field not in list with the same group/field part' => array('not/in/list', false),
        );
    }

    public function testGetPlainList()
    {
        $list = Saas_Saas_Model_DisabledConfiguration_Config::getPlainList();
        $this->assertInternalType('array', $list);
        $this->assertNotEmpty($list);
    }
}
