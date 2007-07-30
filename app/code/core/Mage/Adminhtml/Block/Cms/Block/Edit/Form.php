<?php
/**
 * Adminhtml cms block edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(__('Block Information'));
    }

    protected function _prepareForm()
    {
        $block = Mage::registry('cms_block');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if( intval($block->getBlockId()) > 0 ) {
        	$fieldset->addField('block_id', 'hidden', array(
                'name' => 'block_id',
            ));
        }

    	$fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => __('Block Title'),
            'title' => __('Block Title'),
            'required' => true,
        ));

    	$fieldset->addField('identifier', 'text', array(
            'name' => 'identifier',
            'label' => __('Identifier'),
            'title' => __('Identifier'),
            'required' => true,
        ));

    	$fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => __('Store'),
            'title'     => __('Store'),
            'required'  => true,
            'values'    => array_merge(array(array('value' => 0, 'label' => __('All Stores'))), Mage::getResourceModel('core/store_collection')->load()->toOptionArray()),
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'is_active',
            'required' => true,
            'values'    => array(
                array(
                    'value' => '1',
                    'label' => __('Enabled'),
                ),
                array(
                    'value' => '0',
                    'label' => __('Disabled'),
                )
            )
        ));

    	$fieldset->addField('content', 'editor', array(
            'name' => 'content',
            'label' => __('Content'),
            'title' => __('Content'),
            'style' => 'width: 100%; height: 300px;',
            'wysiwyg' => true,
            'required' => true,
            'theme' => 'advanced',
            'state' => 'html',
        ));

        $form->setValues($block->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return $this;
    }

}
