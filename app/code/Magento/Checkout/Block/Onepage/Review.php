<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

/**
 * One page checkout status
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Review extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'review',
            array('label' => __('Order Review'), 'is_show' => $this->isShow())
        );
        parent::_construct();

        $this->getQuote()->collectTotals()->save();
    }
}
