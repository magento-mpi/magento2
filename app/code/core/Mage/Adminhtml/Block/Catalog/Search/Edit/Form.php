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

class Mage_Adminhtml_Block_Catalog_Search_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_search_form');
        $this->setTitle(__('Search Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_catalog_search');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getId()) {
        	$fieldset->addField('search_id', 'hidden', array(
                'name' => 'search_id',
            ));
        }

    	$fieldset->addField('search_query', 'text', array(
            'name' => 'search_query',
            'label' => __('Search Query'),
            'required' => true,
        ));

        if ($model->getId()) {
	    	$fieldset->addField('num_results', 'text', array(
	            'name' => 'num_results',
	            'label' => __('Number of results<br/>(For last time placed)'),
	            'required' => true,
	        ));
	
	    	$fieldset->addField('popularity', 'text', array(
	            'name' => 'popularity',
	            'label' => __('Number of Uses'),
	            'required' => true,
	        ));
        }
        
        $fieldset->addField('redirect', 'text', array(
            'name' => 'redirect',
            'label' => __('Redirect URL'),
            'after_element_html' => '<span class="hint">' . __('ex. http://domain.com') . '</span>',
        ));

        $fieldset->addField('synonims', 'textarea', array(
            'name' => 'synonims',
            'label' => __('Synonyms'),
            'after_element_html' => '<span class="hint">' . __('(Separate multiple synonyms with commas. Ex: search, seach, serach)') . '</span>',
        ));


        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
