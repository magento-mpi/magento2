<?php
/**
 * Category image attribute frontend
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>

 */
class Mage_Catalog_Model_Entity_Category_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    public function getUrl($object)
    {
        $url = false;
        if ($image = $object->getData($this->getAttribute()->getAttributeCode())) {
            $url = Mage::getSingleton('core/store')->getConfig('catalog/images/category_upload_url').$image;
        }
        return $url;
    }
}
