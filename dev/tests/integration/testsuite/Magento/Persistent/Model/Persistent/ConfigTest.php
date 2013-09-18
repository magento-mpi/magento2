<?php
/**
 * Magento_Persistent_Model_Persistent_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Persistent_Model_Persistent_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Persistent_Model_Persistent_Config
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento_Persistent_Model_Persistent_Config');
    }

    public function testCollectInstancesToEmulate()
    {
        $this->_model->setConfigFilePath(__DIR__ . '/_files/persistent.xml');
        $result = $this->_model->collectInstancesToEmulate();
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testGetBlocks()
    {
        $this->_model->setConfigFilePath(__DIR__ . '/_files/persistent.xml');
        $blocks = $this->_model->getBlocks('//instances/blocks/*[block_type="Magento_Sales_Block_Reorder_Sidebar"]');
        $expected = include '_files/expectedBlocksArray.php';
        $this->assertEquals($expected, $blocks);
    }

}