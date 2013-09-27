<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Poll_Edit_Tab_Answers_List extends Magento_Backend_Block_Template
{
    protected $_template = 'poll/answers/list.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Poll_Model_Poll_AnswerFactory
     */
    protected $_pollAnswerFactory;

    /**
     * @param Magento_Poll_Model_Poll_AnswerFactory $pollAnswerFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Poll_Model_Poll_AnswerFactory $pollAnswerFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_pollAnswerFactory = $pollAnswerFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->_coreRegistry->registry('poll_data')) {
            $this->assign('answers', false);
            return parent::_toHtml();
        }

        $collection = $this->_pollAnswerFactory->create()
            ->getResourceCollection()
            ->addPollFilter($this->_coreRegistry->registry('poll_data')->getId())
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
