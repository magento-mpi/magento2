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
namespace Magento\Checkout\Block\Onepage;

class Review extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('review', array(
            'label'     => __('Order Review'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();

        $this->getQuote()->collectTotals()->save();
    }
}
