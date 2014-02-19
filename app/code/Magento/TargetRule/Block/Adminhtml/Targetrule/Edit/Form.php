<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @var \Magento\Data\FormFactory|null
     */
    protected $_formFactory = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Backend\Helper\Data $backendData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Backend\Helper\Data $backendData,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        $this->_formFactory = $formFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_targetrule_form');
        $this->setTitle(__('Rule Information'));
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(array(
            'data'=> array(
                'id' => 'edit_form',
                'action' => $this->_backendData->getUrl('adminhtml/*/save'),
                'method' => 'post',
            ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
