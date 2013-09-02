<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Locale_Hierarchy_Config_FileResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Locale_Hierarchy_Config_FileResolver
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_appDirsMock;

    protected function setUp()
    {
        $this->_appDirsMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Locale_Hierarchy_Config_FileResolver($this->_appDirsMock);
    }

    /**
     * @covers Magento_Core_Model_Locale_Hierarchy_Config_FileResolver::get
     */
    public function testGet()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        $this->_appDirsMock->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::LOCALE)
            ->will($this->returnValue($path));

        $expectedFilesList = array(
            $path . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml',
            $path . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml'
        );

        $this->assertEquals($expectedFilesList, $this->_model->get('hierarchy_config.xml', 'scope'));
    }
}