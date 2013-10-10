<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Sharing
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\System\Store $systemStore,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);

        $this->systemStore = $systemStore;
        $this->storeManager = $storeManager;
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Sharing Information'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('emails', 'text', array(
            'label'    => __('Emails'),
            'required' => true,
            'class'    => 'validate-emails',
            'name'     => 'emails',
            'note'     => 'Enter list of emails, comma-separated.'
        ));

        if (!$this->storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label'    => __('Send From'),
                'required' => true,
                'name'     => 'store_id',
                'values'   => $this->systemStore->getStoreValuesForForm()
            ));
        }

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name'  => 'message',
            'style' => 'height: 50px;',
            'after_element_html' => $this->getShareButton()
        ));

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
        return $this->getUrl('*/*/share', array('_current' => true));
    }

    /**
     * Create button
     *
     * @return string
     */
    public function getShareButton()
    {
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->addData(array(
                'id'      => '',
                'label'   => __('Share Gift Registry'),
                'type'    => 'submit'
            ))->toHtml();
    }
}
