<?php
/**
 * Layered navigation state
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Layer_State extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/layer/state.phtml');
    }
    
    public function getActiveFilters()
    {
        $filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = array();
        }
        return $filters;
    }
    
    public function getClearUrl()
    {
        return Mage::getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id')));
    }
}
