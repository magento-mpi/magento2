<?php
/**
 * Test for Magento_Filesystem_Adapter_Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_LocalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filesystem_Adapter_Local
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_adapter = new Magento_Filesystem_Adapter_Local();
    }

    /**
     * @param string $key
     * @param bool $expected
     * @dataProvider existsDataProvider
     */
    public function testExists($key, $expected)
    {
        echo $this->_filesPath ;
        $this->assertEquals($expected, $this->_adapter->exists($key));
    }

    /**
     * @return array
     */
    public function existsDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'existed file' => array($filesPath . 'popup.css', true),
            'not existed file' => array($filesPath . 'popup2.css', false),
        );
    }

    /**
     * @param string $fileName
     * @param string $expectedContent
     * @param bool $assert
     * @dataProvider readDataProvider
     */
    public function testRead($fileName, $expectedContent, $assert)
    {
        if ($assert) {
            $this->assertEquals($expectedContent, $this->_adapter->read($fileName));
        } else {
            $this->assertNotEquals($expectedContent, $this->_adapter->read($fileName));
        }
    }

    /**
     * @return array
     */
    public function readDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'correct read' => array($filesPath . 'popup.css', 'var myData = 5;', true),
            'incorrect read' => array($filesPath . 'popup.css', 'var myData = 5; ', false),
            'not existed file' => array($filesPath . 'popup2.css', false, true)
        );
    }
}
