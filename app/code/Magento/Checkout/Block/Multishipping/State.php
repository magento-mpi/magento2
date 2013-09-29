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
 * Multishipping checkout state
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class State extends \Magento\Core\Block\Template
{
    public function getSteps()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping\State')->getSteps();
    }
}
