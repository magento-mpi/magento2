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
        $this->setTemplate('page/menu.phtml');
    }
    
    protected function _beforeToHtml()
    {
        $menu = $this->_buildMenuArray(); 
        $this->assign('menu', $menu);
        return parent::_beforeToHtml();
    }
    
    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        static $baseUrl = null;
        if (is_null($baseUrl)) {
            $baseUrl = Mage::getBaseUrl();
        }
        
        if (is_null($parent)) {
            $parent = Mage::getSingleton('adminhtml/config')->getNode('admin/menu');
        }
        
        $parentArr = array();
        $i = sizeof($parent->children());
        foreach ($parent->children() as $childName=>$child) {
            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }
            if ($child->acl && !$this->_checkAcl($child->acl)) {
                continue;
            }
            $menuArr = array();
            
            $menuArr['label'] = __((string)$child->title);
            $menuArr['title'] = __((string)$child->title);
            
            if ($child->action) {
                $menuArr['url'] = $baseUrl.(string)$child->action;
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }
#print_r($this->getActive().','.$path.$childName."<hr>");
            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);
            
            $menuArr['level'] = $level;
            
            if (--$i==0) {
                $menuArr['last'] = true;
            }

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;
        }
        
        return $parentArr;
    }
    
    protected function _checkDepends(Varien_Simplexml_Element $depends)
    {
        if ($depends->module) {
            $modulesConfig = Mage::getConfig()->getNode('modules');
            foreach ($depends->module as $module) {
                if (!$modulesConfig->$module || !$modulesConfig->$module->is('active')) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    protected function _checkAcl(Varien_Simplexml_Element $acl)
    {
        return true;
        $resource = (string)$acl->resource;
        $privilege = (string)$acl->privilege;        
        return Mage::getSingleton('admin/session')->isAllowed($resource, $privilege);
    }
}
