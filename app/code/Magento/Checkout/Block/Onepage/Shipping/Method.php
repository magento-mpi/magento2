<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage\Shipping;

class Method extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping_method', array(
            'label'     => __('Shipping Method'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
