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
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_banner')->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_banner')->__('Content');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    protected function _prepareForm()
    {
        $banner = Mage::registry('current_banner');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_content');
        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Content'))
        );

        $labels = array();//$banner->getStoreLabels();
        $field = $fieldset->addField('store_default_label', 'textarea', array(
            'name'      => 'store_labels[0]',
            'required'  => false,
            'label'     => Mage::helper('enterprise_banner')->__('Default Store View'),
            'value'     => isset($labels[0]) ? $labels[0] : '',
        ));

        $field->setAfterElementHtml($this->getContentAfterElementHtml($field));

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField('store_'.$group->getId().'_note', 'note', array(
                    'label'    => $website->getName(),
                    'text'     => $group->getName(),
                ));
                foreach ($stores as $store) {
                    $field = $fieldset->addField('store_'.$store->getId().'_label', 'textarea', array(
                        'name'      => 'store_labels['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                    ));

                    $field->setAfterElementHtml($this->getContentAfterElementHtml($field));
                }
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare links for inserting some additional features (widgets, images) to content
     *
     * @param Varien_Data_Form_Element_Abstract
     * @return string
     */
    public function getContentAfterElementHtml($field)
    {
        $links = array();
        $linksHtml = array();

        // Link to media images insertion window
        $winUrl = $this->getUrl('*/cms_page_wysiwyg_images/index');
        $links[] = new Varien_Data_Form_Element_Link(array(
            'href'      => '#',
            'title'     => Mage::helper('enterprise_banner')->__('Insert Image'),
            'value'     => Mage::helper('enterprise_banner')->__('Insert Image'),
            'html_id'   => $field->getId() . '_media',
            'onclick'   => "window.open('" . $winUrl . "', '" . $field->getHtmlId() . "', 'width=1024,height=800')",
        ));

        // Link to widget insertion window
        $winUrl = $this->getUrl('*/cms_widget/index', array('no_wysiwyg' => true));
        $links[] = new Varien_Data_Form_Element_Link(array(
            'href'      => '#',
            'title'     => Mage::helper('enterprise_banner')->__('Insert Widget'),
            'value'     => Mage::helper('enterprise_banner')->__('Insert Widget'),
            'html_id'   => $field->getId() . '_widget',
            'onclick'   => "window.open('" . $winUrl . "', '" . $field->getHtmlId() . "', 'width=1024,height=800')",
        ));

        foreach ($links as $link) {
            $link->setForm($field->getForm());
            $linksHtml[] = $link->getElementHtml();
        }

        return implode(' | ', $linksHtml);
    }
}
