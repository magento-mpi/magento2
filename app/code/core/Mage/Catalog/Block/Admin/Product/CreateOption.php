<?php
/**
 * Product attributes form
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Product_CreateOption extends Mage_Core_Block_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Core', 'form');
        
        // Set form attributes
        $this->setAttribute('method', 'POST');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', Mage::getBaseUrl().'/mage_catalog/product/card/');
        
        $setCollection  = Mage::getModel('catalog', 'product_attribute_set_collection');
        $setCollection->load();
        $arrSets = $setCollection->__toArray();
        
        $setOptions = array();
        foreach ($arrSets['items'] as $item) {
            $setOptions[] = array(
                'label' => $item['product_set_code'],
                'value' => $item['product_attribute_set_id']
            );
        }

        $this->addField('attribute_set', 'select', array(
            'name'  => 'setId',
            'label' => 'Attributes Set',
            'id'    => 'setId',
            'values'=> $setOptions,
            'title' => 'Choose attributes set for new product',
        ));
    }
}