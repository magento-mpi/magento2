<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Locale_Hierarchy_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Locale_Hierarchy_Loader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config_Locales', array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Locale_Hierarchy_Loader($this->_configMock);
    }

    /**
     * @dataProvider composeLocaleHierarchyDataProvider
     */
    public function testComposeLocaleHierarchy($localeConfig, $localeHierarchy)
    {
        $node = new \Magento\Simplexml\Element($localeConfig);
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Magento_Core_Model_Locale_Hierarchy_Loader::XML_PATH_LOCALE_INHERITANCE)
            ->will($this->returnValue($node));
        $this->assertEquals($localeHierarchy, $this->_model->load());
    }

    public function composeLocaleHierarchyDataProvider()
    {
        return array(
            array(
                'xml' => '<config><en_US>en_UK</en_US><en_UK>pt_BR</en_UK></config>',
                array(
                    'en_US' => array('pt_BR', 'en_UK'),
                    'en_UK' => array('pt_BR'),
                )
            ),
            array(
                'xml' => '<config><en_US>en_UK</en_US><en_UK>en_US</en_UK></config>',
                array(
                    'en_US' => array('en_UK'),
                    'en_UK' => array('en_US'),
                )
            ),
            array(
                'xml' => '<config><en_US/><en_UK>wrong_locale</en_UK></config>',
                array(
                    'en_US' => array(''),
                    'en_UK' => array('wrong_locale'),
                )
            ),
            array(
                'xml' => '<config></config>',
                array()
            ),
        );
    }
}
