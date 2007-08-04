<?php
/**
 * Category image attribute backend
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Catalog_Model_Entity_Category_Attribute_Backend_Gallery extends Mage_Eav_Model_Entity_Attribute_Backend_Gallery
{

    public function __construct()
    {
        parent::__construct();
        $this->setConnection(Mage::getSingleton('core/resource')->getConnection('catalog_read'),Mage::getSingleton('core/resource')->getConnection('catalog_write'));
        $this->_imageTypes = array(0, 1, 2);
    }

}
