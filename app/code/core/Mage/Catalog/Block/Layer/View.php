<?php
/**
 * Catalog layered navigation view block
 *
 * @package     Mag
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Layer_View extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/layer/view.phtml');
    }
    
    public function getFilterGroups()
    {
        
    }
}
