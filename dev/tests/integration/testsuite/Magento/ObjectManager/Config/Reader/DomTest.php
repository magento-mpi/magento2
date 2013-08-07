<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Config_Reader_DomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager_Config_Reader_Dom
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var DOMDocument
     */
    protected $_mergedConfig;

    /**
     * @var Magento_ObjectManager_Config_Mapper_Dom
     */
    protected $_mapper;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/../../_files') . DIRECTORY_SEPARATOR;
        $this->_fileList = array(
            $fixturePath . 'config_one.xml',
            $fixturePath . 'config_two.xml',
        );

        $this->_mapper = new Magento_ObjectManager_Config_Mapper_Dom();
        $this->_mergedConfig = new DOMDocument();
        $this->_mergedConfig->load($fixturePath . 'config_merged.xml');
    }

    public function testRead()
    {
        $model = new Magento_ObjectManager_Config_Reader_Dom($this->_fileList, $this->_mapper);
        $this->assertEquals($this->_mapper->map($this->_mergedConfig), $model->read());
    }

}
