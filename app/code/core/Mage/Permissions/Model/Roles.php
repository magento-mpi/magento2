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
        return $this->_buildResourcesArray();
    }

    public function getResourcesList2D()
    {
    	return $this->_buildResourcesArray(null, null, null, true);
    }

    protected function _buildResourcesArray(Varien_Simplexml_Element $resource=null, $parentName=null, $level=0, $represent2Darray=null)
    {
        static $result;
        if (is_null($resource)) {
            $config = new Varien_Simplexml_Config();
            $config->loadFile(Mage::getModuleDir('etc', 'Mage_Admin').DS.'admin.xml');
            $resource = $config->getNode("admin/acl/resources");
            $resourceName = null;
            $level = -1;
            unset($config);
        } else {
        	$resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
        	if ( is_null($represent2Darray) ) {
        		$result[$resourceName]['name'] 	= __((string)$resource->attributes()->title);
        		$result[$resourceName]['level'] = $level;
        	} else {
        		$result[] = $resourceName;
        	}
        }
        $children = $resource->children();
        if (empty($children)) {
            return $result;
        }
        foreach ($children as $child) {
            $this->_buildResourcesArray($child, $resourceName, $level+1, $represent2Darray);
        }
        return $result;
    }

}
?>