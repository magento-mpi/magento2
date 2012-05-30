<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Config
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    public function __construct(array $arguments = array())
    {
        $this->_appConfig = isset($arguments['appConfig']) ? $arguments['appConfig'] : Mage::getConfig();
    }

    /**
     * @return Varien_Simplexml_Config
     */
    public function getTree()
    {
        $director = Mage::getModel('Mage_Backend_Model_Menu_Builder_Director_Dom', array('config' => $this->_getDom()));
        $simpleXmlTree = new Varien_Simplexml_Config();
        $builder = Mage::getModel('Mage_Backend_Model_Menu_Builder_Simplexml', array('tree' => $simpleXmlTree));
        $director->command($builder);
        return $builder->getResult();
    }

    /**
     * @return DOMDocument
     */
    protected function _getDom()
    {
        $fileList = $this->getMenuConfigurationFiles();
        return Mage::getModel('Mage_Backend_Model_Menu_Config_Menu', $fileList)->getMergedConfig();
    }

    /**
     * Return array menu configuration files
     *
     * @return array
     */
    public function getMenuConfigurationFiles()
    {
        $files = $this->_appConfig
            ->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'menu.xml');
        return (array) $files;
    }
}
