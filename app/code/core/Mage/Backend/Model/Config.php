<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config
{
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
        $fileList = array();
        return Mage::getModel('Mage_Backend_Model_Config_Menu', $fileList)->getMergedConfig();
    }
}
