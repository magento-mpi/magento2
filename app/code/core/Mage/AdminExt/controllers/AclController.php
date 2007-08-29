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
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Admin_AclController extends Mage_Core_Controller_Front_Action
{
    public function userTreeAction()
    {
        $users = Mage::getResourceModel('admin/user_collection')->setOrder('firstname,lastname')->loadData();
        $userTree = $this->_buildUserTree($users);
        $this->getResponse()->setBody(Zend_Json::encode($userTree));
    }
    
    public function _buildUserTree(Varien_Data_Collection_Db $users)
    {
        $nodesArr = array();
        foreach ($users->getItems() as $user) {
            $nodesArr[] = array(
                'allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>true, 
                'type'=>'user',
                'id'=>Mage_Admin_Model_Acl::ROLE_TYPE_USER.$user->getUserId(), 
                'text'=>$user->getFirstname().' '.$user->getLastname(), 
            );
        }
        return $nodesArr;
    }
    
    public function roleTreeAction()
    {
        $roles = Mage::getResourceModel('admin/acl_role_collection')->loadData();
        $roleTree = $this->_buildRoleTree($roles);
        $this->getResponse()->setBody(Zend_Json::encode($roleTree));
    }
    
    public function _buildRoleTree(Varien_Data_Collection_Db $roles, $parentId=0)
    {
        $nodesArr = array();
        foreach ($roles->getItems() as $role) {
            if ($role->getRoleType()!=Mage_Admin_Model_Acl::ROLE_TYPE_GROUP
                || $role->getParentId()!=$parentId) {
                continue;
            }
            $nodesArr[] = array(
                'allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>false,
                'type'=>'role',
                'id'=>$role->getRoleType().$role->getRoleId(),
                'text'=>$role->getRoleName(),
                'children'=>$this->_buildRoleTree($roles, $role->getRoleId()),
            );
        }
        return $nodesArr;
    }
    
    public function resourceTreeAction()
    {
        $resources = Mage::getSingleton('admin/config')->getNode('admin/acl/resources');
        $result = $this->_buildResourceTree($resources);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _buildResourceTree(Varien_Simplexml_Element $parentNode, $path='')
    {
        $nodesArr = array();
        foreach ($parentNode->children() as $node) {
            $nodesArr[] = array(
                'allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>false,
                'type'=>'resource',
                'id'=>$path.$node->getName(),
                'text'=>$node->getName(),
                'children'=>$this->_buildResourceTree($node, $path.$node->getName().'/'),
            );
        }
        return $nodesArr;
    }
}