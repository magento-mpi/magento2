<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sagepay Direct payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Checkout\Payment\Sagepay;

class Direct extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Whether to include billing parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendBilling = true;

    /**
     * Whether to include shipping parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendShipping = true;
}
