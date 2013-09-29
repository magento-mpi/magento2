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
 * Mustishipping checkout base abstract block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class AbstractMultishipping extends \Magento\Core\Block\Template
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping');
    }
}
