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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms page edit form revisions tab
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    public function __construct()
    {
        parent::__construct();

        if ($this->_isAllowedAction('delete_revision')) {
            $this->_addButton('delete_revision', array(
                'label'     => Mage::helper('enterprise_cms')->__('Delete'),
                'onclick'   => 'confirmSetLocation(\'' .
                    $this->__('Are you sure you want to do this?') . '\',\'' .
                        $this->getUrl('*/*/deleteRevision', array('_current' => true)) . '\')',
                'class'     => 'delete',
            ));
        }

        if ($this->_isAllowedAction('publish_revision')) {
            if ($this->_isAllowedAction('save')) {
                $this->_addButton('saveandpublish', array(
                    'label'     => Mage::helper('enterprise_cms')->__('Save And Publish'),
                    'onclick'   => 'saveAndPublish(\'' . $this->__('Are you sure you want to do this?') . '\')',
                    'class'     => 'save',
                ));

                $this->_formScripts[] = "
                    function saveAndPublish(){
                        if(confirm(message)) {
                            editForm.submit($('edit_form').action+'back/publishRevision/');
                        }
                    }
                ";
            }

            $this->_addButton('publish_revision', array(
                'label'     => Mage::helper('enterprise_cms')->__('Publish'),
                'onclick'   => 'confirmSetLocation(\'' .
                    $this->__('Are you sure you want to do this?') . '\',\'' .
                    $this->getUrl('*/*/publishRevision', array('_current' => true)). '\')',
                'class'     => 'save',
            ));
        }

        if ($this->_isAllowedAction('save')) {
            //$this->_updateButton('saveandcontinue', 'label', Mage::helper('enterprise_cms')->__('Save And Continue Edit'));
            $this->_updateButton('saveandcontinue', 'level', 4);
            $this->_updateButton('save', 'label', Mage::helper('enterprise_cms')->__('Save'));
            $this->_updateButton('save', 'level', 5);
        }
    }
}
