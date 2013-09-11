<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Data helper
 */
namespace Magento\Paypal\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param \Magento\Paypal\Model\Config $config
     * @param int $customerId
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement(\Magento\Paypal\Model\Config $config, $customerId)
    {
        if (null === self::$_shouldAskToCreateBillingAgreement) {
            self::$_shouldAskToCreateBillingAgreement = false;
            if ($customerId && $config->shouldAskToCreateBillingAgreement()) {
                if (\Mage::getModel('Magento\Sales\Model\Billing\Agreement')->needToCreateForCustomer($customerId)) {
                    self::$_shouldAskToCreateBillingAgreement = true;
                }
            }
        }
        return self::$_shouldAskToCreateBillingAgreement;
    }

    /**
     * Return backend config for element like JSON
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function getElementBackendConfig(\Magento\Data\Form\Element\AbstractElement $element) {
        $config = $element->getFieldConfig();
        if (!array_key_exists('backend_congif', $config)) {
            return false;
        }

        $config = $config['backend_congif'];
        if (isset($config['enable_for_countries'])) {
            $config['enable_for_countries'] = explode(',', str_replace(' ', '', $config['enable_for_countries']));
        }
        if (isset($config['disable_for_countries'])) {
            $config['disable_for_countries'] = explode(',', str_replace(' ', '', $config['disable_for_countries']));
        }
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($config);
    }
}
