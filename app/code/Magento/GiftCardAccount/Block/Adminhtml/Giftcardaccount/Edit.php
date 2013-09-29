<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
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

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Magento_GiftCardAccount';

        parent::_construct();

        $clickSave = "\$('_sendaction').value = 0;";
        $clickSave .= "\$('_sendrecipient_email').removeClassName('required-entry');";
        $clickSave .= "\$('_sendrecipient_name').removeClassName('required-entry');";

        $this->_updateButton('save', 'label', __('Save'));
        $this->_updateButton('save', 'onclick', $clickSave);
        $this->_updateButton('save', 'data_attribute', array(
            'mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#edit_form'),
            ),
        ));
        $this->_updateButton('delete', 'label', __('Delete'));

        $clickSend = "\$('_sendrecipient_email').addClassName('required-entry');";
        $clickSend .= "\$('_sendrecipient_name').addClassName('required-entry');";
        $clickSend .= "\$('_sendaction').value = 1;";

        $this->_addButton('send', array(
            'label'     => __('Save & Send Email'),
            'onclick'   => $clickSend,
            'class'     => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            )
        ));
    }

    public function getGiftcardaccountId()
    {
        return $this->_coreRegistry->registry('current_giftcardaccount')->getId();
    }

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_giftcardaccount')->getId()) {
            $code = $this->escapeHtml($this->_coreRegistry->registry('current_giftcardaccount')->getCode());
            return __('Edit Gift Card Account: %1', $code);
        } else {
            return __('New Gift Card Account');
        }
    }

}
