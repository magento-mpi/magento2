<?php
/**
 * Store switcher block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Store_Switcher extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('store/switcher.phtml');
    }
    
    public function getWebsiteCollection()
    {
        return Mage::getSingleton('core/website')->getResourceCollection()
            ->load();
    }
    
    public function getSwitchUrl()
    {
        return Mage::getUrl('*/*/*', array('_current'=>true, 'store'=>null));
    }
    
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }
}
