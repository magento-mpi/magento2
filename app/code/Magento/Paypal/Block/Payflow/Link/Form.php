<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return $this->getUrl('paypal/payflow/form', ['_secure' => true]);
    }
}
