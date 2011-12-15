<?php

class Mage_Api2_Model_Acl_Filter
{
    const OPERATION_READ  = 'read';
    const OPERATION_WRITE = 'write';

    protected $resourceType;
    protected $userType;

    public function in(array $data, $resourceType = null, $userType = null)
    {
        $operation = self::OPERATION_WRITE;
        
        if ($resourceType===null) {
            $resourceType = $this->resourceType;
        }

        if ($userType===null) {
            $userType = $this->userType;
        }

        $data = $this->filter($resourceType, $operation, $userType, $data);

        return $data;
    }

    public function out(array $data, $resourceType = null, $userType = null)
    {
        $operation = self::OPERATION_READ;
        
        if ($resourceType===null) {
            $resourceType = $this->resourceType;
        }
        
        if ($userType===null) {
            $userType = $this->userType;
        }

        $data = $this->filter($resourceType, $operation, $userType, $data);
        
        return $data;
    }

    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    protected function filter($resourceType, $operation, $userType, $data)
    {
        $allowed = $this->getAllowedAttributes($resourceType, $operation, $userType);

        //TODO use whatever is faster

        /*$filtered = array();
        foreach ($allowed as $attribute) {
            if (isset($data[$attribute])) {
                $filtered[$attribute] = $data[$attribute];
            }
        }*/

        foreach ($data as $attribute=>$value) {
            if (!in_array($attribute, $allowed)) {
                unset($data[$attribute]);
            }
        }

        return $data;
    }

    protected function getAllowedAttributes($resourceType, $operation, $userType)
    {
        //TODO backend to get real attributes allowed

        if ($operation==self::OPERATION_READ) {
            $attributes = array('id', 'entity_id', 'name', 'title', 'sku');
        } else {
            $attributes = array('name');
        }

        return $attributes;
    }
}
