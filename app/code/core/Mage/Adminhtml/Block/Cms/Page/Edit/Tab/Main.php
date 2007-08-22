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
        $model = Mage::registry('cms_page');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getPageId()) {
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
            'label' => __('SEF URL Identifier'),
            'title' => __('SEF URL Identifier'),
            'required' => true,
            'after_element_html' => '<span class="hint">' . __('(eg: domain.com/identifier)') . '</span>',
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

        $layouts = array();
        foreach (Mage::getConfig()->getNode('global/cms/layouts')->children() as $layoutName=>$layoutConfig) {
        	$layouts[$layoutName] = (string)$layoutConfig->label;
        }
    	$fieldset->addField('root_template', 'select', array(
            'name'      => 'root_template',
            'label'     => __('Layout'),
            'required'  => true,
            'options'    => $layouts,
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Page Status'),
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

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
