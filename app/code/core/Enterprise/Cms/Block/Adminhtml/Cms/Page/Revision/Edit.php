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
 * Edit revision page
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{

    /**
     * Constructor. Modifying default CE buttons.
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'revision_id';

        $this->_controller = 'adminhtml_cms_page_revision';
        $this->_blockGroup = 'enterprise_cms';

        $this->setFormActionUrl($this->getUrl('*/cms_page_revision/save'));

        $this->_addButton('preview', array(
            'label'     => Mage::helper('enterprise_cms')->__('Preview'),
            'onclick'   => 'previewAction(\'' . $this->getPreviewUrl() . '\')',
            'class'     => 'preview',
        ));

        $this->_formScripts[] = "
            function previewAction(url){
                $('edit_form').writeAttribute('target', '_blank');
                editForm.submit(url);
                $('edit_form').writeAttribute('target', '');
            }
        ";

        if ($this->_isAllowedAction('publish_revision')) {
            $this->_addButton('publish', array(
                'id'        => 'publish_button',
                'label'     => Mage::helper('enterprise_cms')->__('Select For Publishing'),
                'onclick'   => 'publishAction(\'' . $this->getPublishUrl() . '\')',
                'class'     => 'publish',
            ));

            $this->_formScripts[] = "
                var isDataChanged = false;
                function publishAction(url){
                    if (isDataChanged) {
                        editForm.submit('" . $this->getSaveUrl() . "' + 'back/publish/');
                    } else {
                        setLocation(url);
                    }
                }

                function dataChanged() {
                    isDataChanged = true;
                    var button = $('publish_button');
                    if (button) {
                        button.select('span')[0].update('" . Mage::helper('enterprise_cms')->__('Save And Select For Publishing') . "')
                    }
                }

                varienGlobalEvents.attachEventHandler('tinymceChange', dataChanged());
            ";
        }

        if ($this->_isAllowedAction('save_revision')) {
            $this->_updateButton('save', 'label', Mage::helper('enterprise_cms')->__('Save'));
            $this->_updateButton('save', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\');');
            $this->_updateButton('saveandcontinue', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\'+\'back/edit/\');');
        }

        if ($this->_isAllowedAction('delete_revision')) {
            $this->_updateButton('delete', 'label', Mage::helper('enterprise_cms')->__('Delete'));
        }

        return $this;
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and revision
     *
     * @return string
     */
    public function getHeaderText()
    {
        $revisionNumber = Mage::registry('cms_page')->getRevisionNumber();
        $title = $this->htmlEscape(Mage::registry('cms_page')->getTitle());
        if ($revisionNumber) {
            return Mage::helper('enterprise_cms')->__("Edit Page '%s' Revision #%s", $title, $this->htmlEscape($revisionNumber));
        } else {
            return Mage::helper('enterprise_cms')->__("Edit Page '%s' New Revision", $title);
        }
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'save') {
            $action = 'save_revision';
        }
        return parent::_isAllowedAction($action);
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
                'tab' => 'revisions'
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
     * Get URL for publish button
     *
     * @return string
     */
    public function getPublishUrl()
    {
        return $this->getUrl('*/*/publish', array('_current' => true));
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Adding info block html before form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('revision_info') . parent::getFormHtml();
    }
}
