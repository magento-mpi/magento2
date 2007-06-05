<?php
/**
 * category upload saver model
 *
 * @package     Mage
 * @subpackage  catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Category_Saver_Upload extends Mage_Catalog_Model_Category_Attribute_Saver
{
    public function save($categoryId, $value)
    {
        if ($value = $this->_uploadFile()) {
            parent::save($categoryId, $value);
        }
        return $this;
    }
    
    protected function _uploadFile()
    {
        $this->_attribute->getFormFieldName();
    }
}
