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

class Mage_Adminhtml_Block_System_Website_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('website_form');
        $this->setTitle(__('Website Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('admin_current_website');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getWebsiteId()) {
        	$fieldset->addField('website_id', 'hidden', array(
                'name' => 'website_id',
            ));
        }

    	$fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Website Name'),
            'title' => __('Website Name'),
            'required' => true,
        ));

    	$fieldset->addField('code', 'text', array(
            'name' => 'code',
            'label' => __('Website Code'),
            'title' => __('Website Code'),
            'required' => true,
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'is_active',
            'required' => true,
            'options' => array(
                0=>__('Disabled'),
                1=>__('Enabled'),
            ),
        ));

    	$fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => __('Sort order'),
            'title' => __('Sort order'),
        ));
        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
