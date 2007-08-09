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
        $model = Mage::registry('cms_block');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getBlockId()) {
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

        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();

    	$fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => __('Store'),
            'title'     => __('Store'),
            'required'  => true,
            'options'    => $stores,
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ),
        ));

    	$fieldset->addField('content', 'editor', array(
            'name' => 'content',
            'label' => __('Content'),
            'title' => __('Content'),
            'style' => 'width: 98%; height: 600px;',
            'wysiwyg' => true,
            'required' => true,
            'theme' => 'advanced',
            'state' => 'html',
        ));

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
