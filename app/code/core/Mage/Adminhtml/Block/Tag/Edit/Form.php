<?php
/**
 * Adminhtml tag edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_form');
        $this->setTitle(__('Block Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('tag_tag');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

//        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getTagId()) {
        	$fieldset->addField('tag_id', 'hidden', array(
                'name' => 'tag_id',
            ));
        }

    	$fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Tag Name'),
            'title' => __('Tag Name'),
            'required' => true,
        ));

    	$fieldset->addField('status', 'select', array(
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'status',
            'required' => true,
            'options' => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ),
        ));

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
