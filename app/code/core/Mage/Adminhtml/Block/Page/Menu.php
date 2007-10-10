<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml menu block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            $i--;
			$aclResource = 'admin/'.$path.$childName;
        	if (!$this->_checkAcl($aclResource)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = __((string)$child->title);
            $menuArr['title'] = __((string)$child->title);

            if ($child->action) {
                $menuArr['url'] = Mage::getUrl((string)$child->action);
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }
			#print_r($this->getActive().','.$path.$childName."<hr>");
            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($i==0) {
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

    /*protected function _checkAcl(Varien_Simplexml_Element $acl)
    {
        return true;
        $resource = (string)$acl->resource;
        $privilege = (string)$acl->privilege;
        return Mage::getSingleton('admin/session')->isAllowed($resource, $privilege);
    }*/

    protected function _checkAcl($resource)
    {
        try {
            $res =  Mage::getSingleton('admin/session')->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        return $res;
    }
}
