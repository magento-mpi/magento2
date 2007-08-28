<?php
/**
 * Catalog category landing page attribute source
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $layouts = array();
            foreach (Mage::getConfig()->getNode('global/cms/layouts')->children() as $layoutName=>$layoutConfig) {
            	$this->_options[] = array(
            	   'value'=>$layoutName,
            	   'label'=>(string)$layoutConfig->label
            	);
            }
            array_unshift($this->_options, array('value'=>'', 'label'=>__('No layout updates')));
        }
        return $this->_options;
    }
}
