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

namespace Magento\Adminhtml\Block\Poll\Edit\Tab\Answers;

class ListAnswers extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'poll/answers/list.phtml';

    protected function _toHtml()
    {
        if( !\Mage::registry('poll_data') ) {
            $this->assign('answers', false);
            return parent::_toHtml();
        }

        $collection = \Mage::getModel('Magento\Poll\Model\Poll\Answer')
            ->getResourceCollection()
            ->addPollFilter(\Mage::registry('poll_data')->getId())
            ->load();
        $this->assign('answers', $collection);

        return parent::_toHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('deleteButton', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete'),
            'class' => 'action-delete action- delete icon-btn'
        ));

        $this->addChild('addButton', '\Magento\Adminhtml\Block\Widget\Button', array(
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
