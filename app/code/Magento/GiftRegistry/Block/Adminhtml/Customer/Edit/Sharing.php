<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Sharing extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->systemStore = $systemStore;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            array('data' => array('id' => 'edit_form', 'action' => $this->getActionUrl(), 'method' => 'post'))
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('Sharing Information'), 'class' => 'fieldset-wide')
        );

        $fieldset->addField(
            'emails',
            'text',
            array(
                'label' => __('Emails'),
                'required' => true,
                'class' => 'validate-emails',
                'name' => 'emails',
                'note' => 'Enter list of emails, comma-separated.'
            )
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'select',
                array(
                    'label' => __('Send From'),
                    'required' => true,
                    'name' => 'store_id',
                    'values' => $this->systemStore->getStoreValuesForForm()
                )
            );
        }

        $fieldset->addField(
            'message',
            'textarea',
            array(
                'label' => __('Message'),
                'name' => 'message',
                'style' => 'height: 50px;',
                'after_element_html' => $this->getShareButton()
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setDataObject();

        return parent::_prepareForm();
    }

    /**
     * Return sharing form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/*/share', array('_current' => true));
    }

    /**
     * Create button
     *
     * @return string
     */
    public function getShareButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->addData(
            array('id' => '', 'label' => __('Share Gift Registry'), 'type' => 'submit')
        )->toHtml();
    }
}
