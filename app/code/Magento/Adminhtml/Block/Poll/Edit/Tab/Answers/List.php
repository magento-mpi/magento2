<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Poll_Edit_Tab_Answers_List extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'poll/answers/list.phtml';

    protected function _toHtml()
    {
        if( !Mage::registry('poll_data') ) {
            $this->assign('answers', false);
            return parent::_toHtml();
        }

        $collection = Mage::getModel('Magento_Poll_Model_Poll_Answer')
            ->getResourceCollection()
            ->addPollFilter(Mage::registry('poll_data')->getId())
            ->load();
        $this->assign('answers', $collection);

        return parent::_toHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('deleteButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Delete'),
            'class' => 'action-delete action- delete icon-btn'
        ));

        $this->addChild('addButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Add New Answer'),
            'class' => 'action-add'
        ));
        return parent::_prepareLayout();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }
}
