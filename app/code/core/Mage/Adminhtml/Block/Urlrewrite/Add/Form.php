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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml add product urlrewrite form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */

class Mage_Adminhtml_Block_Urlrewrite_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_urlrewrite_form', array('legend' => Mage::helper('urlrewrite')->__('General Information')));

        $fieldset->addField('product_id', 'hidden', array(
	        'name' => 'product_id'
        ));

        $fieldset->addField('product_name', 'note', array(
                                'label'     => Mage::helper('urlrewrite')->__('Product'),
                                'text'      => 'product_name',
                            )
        );

        $fieldset->addField('category_name', 'note', array(
                                'label'     => Mage::helper('urlrewrite')->__('Category'),
                                'text'      => 'category_name',
                            )
        );

		$stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();
        $fieldset->addField('store_id', 'select', array(
	        'label' 		=> $this->__('Store'),
	        'title' 		=> $this->__('Store'),
	        'name' 			=> 'store_id',
	        'required' 		=> true,
	        'options'		=> $stores
        ));

        $fieldset->addField('id_path', 'text', array(
	        'label' 		=> $this->__('ID Path'),
	        'title' 		=> $this->__('ID Path'),
	        'name' 			=> 'id_path',
	        'required' 		=> true,
        ));

    	$fieldset->addField('request_path', 'text', array(
            'label' 		=> $this->__('Request Path'),
            'title' 		=> $this->__('Request Path'),
            'name' 	=> 'request_path',
            'required' 		=> true,
        ));

		$fieldset->addField('target_path', 'text', array(
            'label'			=> $this->__('Target Path'),
            'title'			=> $this->__('Target Path'),
            'name'			=> 'target_path',
            'required'		=> true,
        ));

    	$fieldset->addField('options', 'select', array(
            'label' 	=> $this->__('Options'),
            'title' 	=> $this->__('Options'),
            'name' 		=> 'options',
            'required' 	=> true,
            'options'	=> array(
            	Mage_Urlrewrite_Model_Urlrewrite::OPTIONS_REWRITE   => $this->__('Rewrite'),
                Mage_Urlrewrite_Model_Urlrewrite::OPTIONS_REDIRECT  => $this->__('Redirect'),
            ),
        ));

    	$fieldset->addField('description', 'textarea', array(
            'label' 		=> $this->__('Description'),
            'title' 		=> $this->__('Description'),
            'name' 			=> 'description',
            'cols'			=> 20,
            'rows'			=> 5,
            'wrap'			=> 'soft'
        ));

        $gridFieldset = $form->addFieldset('add_urlrewrite_grid', array('legend' => Mage::helper('urlrewrite')->__('Please select a product')));
        $gridFieldset->addField('products_grid', 'note', array(
            'text' => $this->getLayout()->createBlock('adminhtml/urlrewrite_product_grid')->toHtml(),
        ));

        $gridFieldset = $form->addFieldset('add_urlrewrite_category', array('legend' => Mage::helper('urlrewrite')->__('Please select a category')));
        $gridFieldset->addField('category_tree', 'note', array(
            //'text' => 'Category'//$this->getLayout()->createBlock('adminhtml/urlrewrite_category_tree')->toHtml(),
            'text' => $this->getLayout()->createBlock('adminhtml/urlrewrite_category_tree')->toHtml(),
        ));

        $gridFieldset = $form->addFieldset('add_urlrewrite_type', array('legend' => Mage::helper('urlrewrite')->__('Please select a type')));
        $gridFieldset->addField('type', 'select', array(
	        'label' 	=> $this->__('Type'),
	        'title' 	=> $this->__('Type'),
	        'name' 		=> 'type',
	        'required' 	=> true,
	        'options'	=> array('' => '',
	        Mage_Urlrewrite_Model_Urlrewrite::TYPE_CATEGORY => $this->__('Category'),
	        Mage_Urlrewrite_Model_Urlrewrite::TYPE_PRODUCT  => $this->__('Product'),
	        Mage_Urlrewrite_Model_Urlrewrite::TYPE_CUSTOM   => $this->__('Custom')
	        )
        ));

        $form->setMethod('POST');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        //$form->setAction(Mage::getUrl('*/*/post'));

        //$form->setUseContainer(true);
        $form->setAction( $form->getAction() . 'ret/' . $this->getRequest()->getParam('ret') );
        //$this->setForm($form);

        $this->setForm($form);
    }
}