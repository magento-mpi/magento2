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
 * Order create errors block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Messages extends \Magento\Adminhtml\Block\Messages
{

    protected function _prepareLayout()
    {
        $this->addMessages(\Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote')->getMessages(true));
        parent::_prepareLayout();
    }

}
