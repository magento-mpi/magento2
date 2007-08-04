<?php

class Mage_Eav_Model_Entity_Attribute_Backend_Gallery_Image extends Varien_Object
{

    protected $_imageType = '';

    public function getSourceUrl()
    {

        $url = Mage::getBaseUrl() . Mage::getSingleton('core/store')->getConfig('web/url/upload') . $this->getType() . '/' . $this->getValue();
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