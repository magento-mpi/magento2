<?php
/**
 * Product image attribute saver
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Saver_Image extends Mage_Catalog_Model_Product_Attribute_Saver 
{
    function save($productId, $value)
    {
        if ($value = $this->_uploadFile()) {
            parent::save($productId, $value);
        }
        return $this;
    }

    protected function _uploadFile()
    {
        $uploadFile = new Varien_File_Uploader($this->_attribute->getFormFieldName());
        $uploadFile->setFilesDispersion(true);
        $uploadFile->save(Mage::getBaseDir('upload') . DIRECTORY_SEPARATOR . 'products');
        return $uploadFile->getUploadedFileName();
    }
}
