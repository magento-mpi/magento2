<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

class Review extends AbstractOnepage
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
    }
}
