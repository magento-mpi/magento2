<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Payflow\Link;

/**
 * Payflow link iframe block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Payment\Block\Form
{
    /**
     * @var string
     */
    protected $_template = 'payflowlink/info.phtml';

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflow/form', array('_secure' => true));
    }
}
