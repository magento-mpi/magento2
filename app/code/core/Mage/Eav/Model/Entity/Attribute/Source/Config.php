<?php

/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Eav_Model_Entity_Attribute_Source_Config extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options for the source from configuration
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $rootNode = false;
            if ($this->getConfig()->rootNode) {
                $rootNode = Mage::getConfig()->getNode((string)$this->getConfig()->rootNode);
            } elseif ($this->getConfig()->rootNodeXpath) {
                $rootNode = Mage::getConfig()->getXpath((string)$this->getConfig()->rootNode);
            }
            
            if (!$rootNode) {
                $rootNode = $this->getConfig()->options;
            }
            
            if (!$rootNode) {
                throw Mage::exception('Mage_Eav', 'No options root node found');
            }
            foreach ($rootNode->children() as $option) {
                //$this->_options[(string)$option->value] = (string)$option->label;
                $this->_options[] = array(
                    'value' => (string)$option->value,
                    'label' => (string)$option->label
                );
            }
        }
        
        return $this->_options;
    }

}