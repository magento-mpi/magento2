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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance edit container
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'instance_id';
        $this->_blockGroup = 'enterprise_cms';
        $this->_controller = 'adminhtml_cms_widget_instance';
        parent::__construct();
    }

    public function getWidgetInstance()
    {
        return Mage::registry('widget_instance');
    }

    public function setWidgetInstance($widgetInstance)
    {
        return $this;
    }

    protected function _preparelayout()
    {
        if ($this->getWidgetInstance()->isCompleteToCreate()) {
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'     => Mage::helper('enterprise_cms')->__('Save and Continue Edit'),
                    'class'     => 'save',
                    'onclick'   => 'saveAndContinueEdit()'
                ),
                100
            );
        } else {
            $this->removeButton('save');
        }
        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        if ($this->getWidgetInstance()->getId()) {
            return Mage::helper('enterprise_cms')->__('Widget "%s"', $this->htmlEscape($this->getWidgetInstance()->getTitle()));
        }
        else {
            return Mage::helper('enterprise_cms')->__('New Widget Instance');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}
