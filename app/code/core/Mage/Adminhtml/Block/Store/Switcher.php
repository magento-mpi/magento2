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
class Mage_Adminhtml_Block_Store_Switcher extends Mage_Adminhtml_Block_Widget 
{
    public function __construct() 
    {
        $this->setTemplate('adminhtml/store/switcher.phtml');
    }
    
    protected function _beforeToHtml()
    {
        $stores = array(
            'store1' => new Varien_Object(array('id'=>1, 'name'=>'Store 1')),
            'store2' => new Varien_Object(array('id'=>2, 'name'=>'Store 2')),
        );
        $this->assign('stores', $stores);
        $this->assign('switchUrl', Mage::getUrl('adminhtml', array('controller'=>'system', 'action'=>'setStore')));
        $this->assign('selectedStoreId', Mage::getSingleton('adminhtml/session')->getStoreId());
        return $this;
    }
}
