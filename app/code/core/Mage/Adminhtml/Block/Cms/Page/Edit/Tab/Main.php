<?php
/**
 * Cms page edit form main tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $page = Mage::registry('cms_page');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if( intval($page->getPageId()) > 0 ) {
        	$fieldset->addField('page_id', 'hidden', array(
                'name' => 'page_id',
            ));
        }

    	$fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => __('Page Title'),
            'title' => __('Page Title'),
            'required' => true,
        ));

    	$fieldset->addField('identifier', 'text', array(
            'name' => 'identifier',
            'label' => __('Identifier'),
            'title' => __('Identifier'),
            'required' => true,
        ));

        $stores = Mage::getResourceModel('core/store_collection')->load()->toOptionHash();
        $stores[0] = __('All stores');

    	$fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => __('Store'),
            'title'     => __('Store'),
            'required'  => true,
            'options'    => $stores,
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Page Status'),
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
            'style' => 'width: 520px; height: 300px;',
            'wysiwyg' => true,
            'required' => true,
            'theme' => 'advanced',
            'state' => 'html',
        ));

        $form->setValues($page->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
