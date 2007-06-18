<?php
/**
 * Adminhtml menu block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page_Menu extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('adminhtml/page/menu.phtml');
    }
    
    protected function _beforeToHtml()
    {
        $menu = $this->_buildMenuArray(); 
        $this->assign('menu', $menu);
        return true;
    }
    
    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='')
    {
        static $baseUrl = null;
        if (is_null($baseUrl)) {
            $baseUrl = Mage::getBaseUrl();
        }
        
        if (is_null($parent)) {
            $parent = Mage::getSingleton('adminhtml/config')->getNode('admin/menu');
        }
        
        $parentArr = array();
        foreach ($parent->children() as $childName=>$child) {
            $menuArr = array();
            
            $menuArr['label'] = (string)$child->title;
            $menuArr['title'] = (string)$child->title;
            
            if ($child->action) {
                $menuArr['url'] = $baseUrl.(string)$child->action.'/';
            } else {
                $menuArr['url'] = '#';
            }
            
            $menuArr['active'] = $this->getActive()==$path.$childName;
            
            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/');
            }

            $parentArr[$childName] = $menuArr;
        }
        
        return $parentArr;
    }
}
