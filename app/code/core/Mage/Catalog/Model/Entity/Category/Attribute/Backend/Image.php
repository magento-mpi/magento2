<?php
/**
 * Category image attribute backend
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        try {
            $uploader = new Varien_File_Uploader($this->getAttribute()->getName());
        }
        catch (Exception $e){
            return;
        }
        
        //$uploader->save($this->getAttribute()->getEntity()->getStore()->getConfig('system/filesystem/upload'));
        $uploader->save(Mage::getSingleton('core/store')->getConfig('system/filesystem/upload'));
        
        if ($uploader->getUploadedFileName()) {
            $object->setData($this->getAttribute()->getName(), $uploader->getUploadedFileName());
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
        }
    }
}
