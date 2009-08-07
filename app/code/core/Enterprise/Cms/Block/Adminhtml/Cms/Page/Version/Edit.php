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
 * Edit version page
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId   = 'version_id';
    protected $_blockGroup = 'enterprise_cms';
    protected $_controller = 'adminhtml_cms_page_version';

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $version = Mage::registry('cms_page_version');

        // Add 'new button' depending on permission
        if (Mage::getSingleton('enterprise_cms/config')->isCurrentUserCanSaveRevision()) {
             $this->_addButton('new', array(
                    'label'     => Mage::helper('adminhtml')->__('Save As New'),
                    'onclick'   => "editForm.submit('" . $this->getNewUrl() . "');",
                    'class'     => 'new',
                ));
        }

        /*
         * Disable Edit or Remove if user not owner of version
         * in other case check permission
         */
        if ($version->getUserId() != Mage::getSingleton('admin/session')->getUser()->getId()) {
            $this->removeButton('save');
            $this->removeButton('delete');
        } else {
            if (!Mage::getSingleton('enterprise_cms/config')->isCurrentUserCanSaveRevision()) {
                $this->removeButton('save');
            } else {
                 $this->_addButton('saveandcontinue', array(
                        'label'     => Mage::helper('enterprise_cms')->__('Save And Continue Edit'),
                        'onclick'   => "editForm.submit($('edit_form').action+'back/edit/');",
                        'class'     => 'save',
                    ));
            }

            if (!Mage::getSingleton('enterprise_cms/config')->isCurrentUserCanDeleteRevision()) {
                $this->removeButton('delete');
            }
        }
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and version
     *
     * @return string
     */
    public function getHeaderText()
    {
        $versionLabel = $this->htmlEscape(Mage::registry('cms_page_version')->getLabel());
        $title = $this->htmlEscape(Mage::registry('cms_page')->getTitle());

        return Mage::helper('enterprise_cms')->__("Edit Page '%s' Version '%s'", $title, $versionLabel);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/cms_page/edit',
             array(
                'page_id' => Mage::registry('cms_page')->getPageId(),
                'tab' => 'versions'
             ));
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }

    /**
     * Get URL for new button
     *
     * @return string
     */
    public function getNewUrl()
    {
        return $this->getUrl('*/*/new', array('_current' => true));
    }

}
