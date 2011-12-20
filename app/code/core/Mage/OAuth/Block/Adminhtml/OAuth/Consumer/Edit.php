<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * OAuth Consumer Edit Block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Adminhtml_OAuth_Consumer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Construct edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oAuth_consumer';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('oauth')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 100);
        $this->_formScripts[] = " function saveAndContinueEdit() { editForm.submit($('edit_form').action + 'back/edit/') } ";

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', $this->__('Delete'));
        if(!$this->getModel()->getId()) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Get consumer model
     *
     * @return Mage_OAuth_Model_Consumer
     */
    public function getModel()
    {
        /** @var $model Mage_OAuth_Model_Consumer */
        $model = Mage::registry('current_consumer');
        return $model;
    }

    /**
     * Get init JavaScript for form
     *
     * @return string
     */
    public function getFormInitScripts()
    {
//        return $this->getLayout()->createBlock('core/template')
//            ->setTemplate('googleshopping/types/edit.phtml')
//            ->toHtml();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if(!is_null($this->getModel()->getId())) {
            return $this->__('Edit attribute set mapping');
        } else {
            return $this->__('New attribute set mapping');
        }
    }
}
