<?php

class Mage_Eav_Model_Entity_Attribute_Backend_Gallery_Image extends Varien_Object
{
    public function __construct()
    {
        $this->setIdFieldName('value_id');
    }
    
    protected $_imageType = '';

    public function getSourceUrl()
    {
        if ($this->getAttribute()->getEntity()->getStoreId() == 0) {
            $url = Mage::getSingleton('core/store')->getConfig('web/url/upload') . $this->getType() . '/' . $this->getValue();
        }
        else {
            $url = $this->getAttribute()->getEntity()->getStore()->getConfig('web/url/upload') . $this->getType() . '/' . $this->getValue();
        }
        return $url;
    }

    public function getType()
    {
        return $this->_imageType;
    }

    public function setType($imageType)
    {
        $this->_imageType = $imageType;
        return $this;
    }

}
