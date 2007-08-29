<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attributes form
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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
        
        $setCollection  = Mage::getResourceModel('catalog/product_attribute_set_collection');
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