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

/**
 * Banner content per store view edit page
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
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
     * Load Wysiwyg on demand and Prepare layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare Banners Content Tab form, define Editor settings
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $banner = Mage::registry('current_banner');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_content');
        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend'=>Mage::helper('enterprise_banner')->__('Content'))
        );

        $storeContents = $banner->getStoreContents();
        $field = $fieldset->addField('store_default_content', 'editor', array(
            'name'      => 'store_contents[0]',
            'required'  => true,
            'label'     => Mage::helper('enterprise_banner')->__('All Store Views'),
            'value'     => isset($storeContents[0]) ? $storeContents[0] : ''
        ));
        $hideTr = '';
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
                    $contentExists = isset($storeContents[$store->getId()]);
                    $contentFieldId = 'store_'.$store->getId().'_content';

                    if (!$contentExists) {
                         $hideTr = '<script language="javascript">Event.observe(window, \'load\', function(){$(\'' . $contentFieldId . '\').hide();})</script>';
                    }
                    else {
                         $hideTr = '';
                    }
                    $onClick ='$(\''. $contentFieldId.'\').toggle();';

                    $fieldset->addField('store_'.$store->getId().'_content_use', 'checkbox', array(
                        'name'      => 'store_contents_not_use['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'onclick'   => $onClick,
                        'checked'   => $contentExists ? false : true,
                        'after_element_html' => '<label class="normal" for="'.$form->getHtmlIdPrefix().'store_'.$store->getId().'_content_use">'.Mage::helper('enterprise_banner')->__('Use Default').'</label>' . $hideTr,
                        'value'     => $store->getId()
                    ));

                    $field = $fieldset->addField($contentFieldId, 'editor', array(
                        'name'      => 'store_contents['.$store->getId().']',
                        'required'  => false,
                        'value'     => $contentExists ? $storeContents[$store->getId()] : '',
                        'container_id' => $contentFieldId
                    ));
                }
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
