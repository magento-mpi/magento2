<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Mage_Saas
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_Tenant_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Saas_Model_Tenant_Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Saas_Saas_Model_Tenant_Config();
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMerge($mergeArray, $expectedResult)
    {
        $this->_model->merge($mergeArray);
        $this->assertInstanceOf('Varien_Simplexml_Config', $this->_model);
        $this->assertXmlStringEqualsXmlString($this->_model->getXmlString(), $expectedResult);
    }

    public function mergeDataProvider()
    {
        $xmlStart = '<?xml version="1.0" encoding="utf-8" ?>';
        $xmlNode = '<node><value>1</value></node>';
        $xmlNode1 = '<node1><value>1</value></node1>';
        $xmlNode1Alt = '<node1><value>2</value></node1>';

        return array(
            'one' => array(
                array($xmlStart . '<config>' . $xmlNode . '</config>'),
                $xmlStart . '<config>' . $xmlNode . '</config>'
            ),
            'two_different' => array(
                array(
                    $xmlStart . '<config>' . $xmlNode . '</config>',
                    $xmlStart . '<config>' . $xmlNode1 . '</config>',
                ),
                $xmlStart . '<config>' . $xmlNode . $xmlNode1 . '</config>'
            ),
            'two_equal' => array(
                array(
                    $xmlStart . '<config>' . $xmlNode1 .  '</config>',
                    $xmlStart . '<config>' . $xmlNode1Alt . '</config>',
                ),
                $xmlStart . '<config>' . $xmlNode1Alt . '</config>'
            ),
        );
    }

    public function testGetModulesConfig()
    {
        $xmlStart = '<?xml version="1.0" encoding="utf-8" ?>';
        $xmlTestModule = '<Test_Module><active>true</active></Test_Module>';
        $xmlTestModule1 = '<Test_Module1><active>true</active></Test_Module1>';

        $this->assertXmlStringEqualsXmlString(
            $this->_model->getModulesConfig(
                $xmlStart . '<config><modules>' . $xmlTestModule . $xmlTestModule1 . '</modules></config>',
                array('Test_Module' => true, 'Test_OtherModule' => true)
            ),
            $xmlStart . '<config><modules>' . $xmlTestModule . '</modules></config>'
        );
    }

    /**
     * @dataProvider loadModulesDataProvider
     */
    public function testLoadModulesFromString($modulesString, $expectedResult)
    {
        $result = $this->_model->loadModulesFromString($modulesString);
        $this->assertInternalType('array', $result);
        $this->assertEquals($result, $expectedResult);
    }

    public function loadModulesDataProvider()
    {
        return array(
            'empty_string' => array(
                '',
                array()
            ),
            'non_empty_string' => array(
                '<?xml version="1.0" encoding="utf-8" ?><config><modules>'
                    . '<Test_Module><active>true</active></Test_Module>'
                    . '<Test_Module1><active>false</active></Test_Module1>'
                    . '</modules></config>',
                array('Test_Module' => array('active' => 'true'), 'Test_Module1' => array('active' => 'false'))
            ),
        );
    }
}
