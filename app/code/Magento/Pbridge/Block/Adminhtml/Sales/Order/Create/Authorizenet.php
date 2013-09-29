<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Authorize.Net payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class Authorizenet extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Authorize payment code
     *
     * @var string
     */
    protected $_code = 'authorizenet';
}
