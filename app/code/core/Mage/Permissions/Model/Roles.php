<?php
class Mage_Permissions_Model_Roles extends Varien_Object {
	public function getResource()
	{
        return Mage::getResourceSingleton('permissions/roles');
    }

    public function load($roleId)
    {
        $this->setData($this->getResource()->load($roleId));
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    public function getCollection()
    {
        return Mage::getResourceModel('permissions/roles_collection');
    }

    public function getUsersCollection()
    {
        return Mage::getResourceModel('permissions/roles_user_collection');
    }

    public function getResourcesList()
    {
        return $this->_buildResourceArray();
    }

    protected function _buildResourceArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        static $result;

        if (is_null($parent)) {
            $parent = Mage::getSingleton('adminhtml/config')->getNode('admin/menu');
        }

        foreach ($parent->children() as $childName=>$child) {
            $result[$path.$childName.'/']['name'] = __((string)$child->title);
            $result[$path.$childName.'/']['level'] = $level;
            if ($child->children) {
                $this->_buildResourceArray($child->children, $path.$childName.'/', $level+1);
            }
        }

        return $result;
    }
}
?>