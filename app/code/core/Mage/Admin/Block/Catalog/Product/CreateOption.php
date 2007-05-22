<?php
/**
 * Product attributes form
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Admin_Block_Catalog_Product_CreateOption extends Mage_Core_Block_Form
{
    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('form.phtml');
        
        // Set form attributes
        $this->setAttribute('method', 'POST');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', Mage::getBaseUrl().'admin/product/card/');
        
        $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection');
        $setCollection->load();
        $arrSets = $setCollection->toArray();
        
        $setOptions = array();
        foreach ($arrSets['items'] as $item) {
            $setOptions[] = array(
                'label' => $item['code'],
                'value' => $item['set_id']
            );
        }
        
        $types = (array) Mage::getConfig()->getNode('global/catalog/product/types');
        
        $typeOptions = array();
        foreach ($types as $typeCode=>$typeInfo) {
            $typeOptions[] = array(
                'label'=>$typeCode,
                'value'=>$typeCode
            );
        }

        $this->addField('attribute_set', 'select', array(
            'name'  => 'set',
            'label' => 'Attributes Set',
            'id'    => 'choose_attribute_set',
            'values'=> $setOptions,
            'title' => 'Choose attributes set for new product',
        ));

        $this->addField('product_type', 'select', array(
            'name'  => 'type',
            'label' => 'Product Type',
            'id'    => 'choose_product_type',
            'values'=> $typeOptions,
            'title' => 'Choose product type',
        ));
    }
}