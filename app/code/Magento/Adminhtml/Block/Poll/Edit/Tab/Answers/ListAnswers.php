<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Block\Poll\Edit\Tab\Answers;

class ListAnswers extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'poll/answers/list.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->_coreRegistry->registry('poll_data')) {
            $this->assign('answers', false);
            return parent::_toHtml();
        }

        $collection = \Mage::getModel('Magento\Poll\Model\Poll\Answer')
            ->getResourceCollection()
            ->addPollFilter($this->_coreRegistry->registry('poll_data')->getId())
            ->load();
        $this->assign('answers', $collection);

        return parent::_toHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('deleteButton', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete'),
            'class' => 'action-delete action- delete icon-btn'
        ));

        $this->addChild('addButton', 'Magento\Adminhtml\Block\Widget\Button', array(
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
