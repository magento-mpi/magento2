<?php

class Mage_Eav_Model_Entity_Attribute_Backend_Gallery_Image_Collection extends Varien_Data_Collection_Db
{

    public function __construct($conn=null)
    {
        parent::__construct($conn);
        $this->setItemObjectClass('Mage_Eav_Model_Entity_Attribute_Backend_Gallery_Image');
    }

}
