<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\View;

/**
 * Adminhtml sales order view gift messages controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Giftmessage extends \Magento\Backend\App\Action
{
    /**
     * Retrieve gift message save model
     *
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get('Magento\GiftMessage\Model\Save');
    }
}
