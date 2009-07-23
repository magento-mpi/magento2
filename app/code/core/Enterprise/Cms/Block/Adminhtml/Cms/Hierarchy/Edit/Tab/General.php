<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Page Tree Edit General Form Tab Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize Edit Form
     *
     */
    public function __construct()
    {
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
        $this->setFieldNameSuffix('cms_hierarchy');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Grid_Tab_General
     */
    protected function _prepareForm()
    {
        /* @var $model Enterprise_Cms_Model_Hierarchy */
        $model      = Mage::registry('current_hierarchy');
        /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
        $node       = Mage::registry('current_hierarchy_node');
        $form       = new Varien_Data_Form();
        $fieldset   = $form->addFieldset('general_fieldset', array(
            'legend'    => Mage::helper('enterprise_cms')->__('General Information')
        ));

        $fieldset->addField('continue_edit', 'hidden', array(
            'name'      => 'continue_edit',
            'value'     => 0
        ));
        $fieldset->addField('tree_id', 'hidden', array(
            'name'      => 'tree_id',
            'value'     => $model->getId()
        ));
        $fieldset->addField('page_id', 'hidden', array(
            'name'      => 'page_id',
            'value'     => $model->getRootNode()->getPageId()
        ));

        $fieldset->addField('nodes_data', 'hidden', array(
            'name'      => 'nodes_data'
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => Mage::helper('enterprise_cms')->__('Identifier (URL Key)'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'value'     => $model->getRootNode()->getIdentifier()
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => Mage::helper('enterprise_cms')->__('Tree Title'),
            'required'  => true,
            'value'     => $model->getRootNode()->getLabel()
        ));

        $pageTitle = Mage::helper('enterprise_cms')->__('Unselected');
        if ($node->getPageTitle()) {
            $pageTitle = $node->getPageTitle();
        }

        $afterHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'select_page_button',
                'label'     => Mage::helper('enterprise_cms')->__('Select'),
                'onclick'   => 'hierarchy.selectPage()',
                'class'     => ''))
            ->toHtml();
        $fieldset->addField('page_id_element', 'note', array(
            'label'     => Mage::helper('enterprise_cms')->__('Base Page'),
            'text'      => $pageTitle,
            'after_element_html' => ' &nbsp; ' . $afterHtml
        ));

        $fieldset   = $form->addFieldset('metadata_fieldset', array(
            'legend'    => Mage::helper('enterprise_cms')->__('Render Metadata in HTML Head')
        ));

        $yesNoOptions = array(
            1 => Mage::helper('enterprise_cms')->__('Yes'),
            0 => Mage::helper('enterprise_cms')->__('No')
        );

        $fieldset->addField('meta_first_last', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('First/Last'),
            'title'     => Mage::helper('enterprise_cms')->__('First/Last'),
            'name'      => 'meta_first_last',
            'options'   => $yesNoOptions,
            'value'     => $model->getMetaFirstLast()
        ));

        $fieldset->addField('meta_next_previous', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Next/Previous'),
            'title'     => Mage::helper('enterprise_cms')->__('Next/Previous'),
            'name'      => 'meta_next_previous',
            'options'   => $yesNoOptions,
            'value'     => $model->getMetaNextPrevious()
        ));

        $fieldset->addField('meta_chapter', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Chapter'),
            'title'     => Mage::helper('enterprise_cms')->__('Chapter'),
            'name'      => 'meta_chapter',
            'options'   => $yesNoOptions,
            'value'     => $model->getMetaChapter()
        ));

        $fieldset->addField('meta_section', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Section'),
            'title'     => Mage::helper('enterprise_cms')->__('Section'),
            'name'      => 'meta_section',
            'options'   => $yesNoOptions,
            'value'     => $model->getMetaSection()
        ));

        $form->addFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve buttons for select base page grid
     *
     * @return string
     */
    public function getPageGridButtonsHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'reset_general_page_button',
                'label'     => Mage::helper('enterprise_cms')->__('Deselect Page'),
                'onclick'   => 'hierarchy.resetPage()',
                'class'     => 'reset'))
            ->toHtml();

        return join(' ', $buttons);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('General');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('General');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
