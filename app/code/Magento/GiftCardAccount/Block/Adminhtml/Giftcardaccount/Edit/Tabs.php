<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcardaccount_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Account'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab('info', array(
            'label'     => __('Information'),
            'content'   => $this->getLayout()->createBlock(
                'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info'
            )->initForm()->toHtml(),
            'active'    => true
        ));

        $this->addTab('send', array(
            'label'     => __('Send Gift Card'),
            'content'   => $this->getLayout()->createBlock(
                'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Send'
            )->initForm()->toHtml(),
        ));

        $model = $this->_coreRegistry->registry('current_giftcardaccount');
        if ($model->getId()) {
            $this->addTab('history', array(
                'label'     => __('History'),
                'content'   => $this->getLayout()->createBlock(
                    'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\History'
                )->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

}
