<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model\Config\Source;

class Allmethods implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_shippingConfig = $shippingConfig;
    }

    /**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag=false)
    {
        $methods = array(array('value'=>'', 'label'=>''));
        $carriers = $this->_shippingConfig->getAllCarriers();
        foreach ($carriers as $carrierCode=>$carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = $this->_coreStoreConfig->getConfig('carriers/'.$carrierCode.'/title');
            $methods[$carrierCode] = array(
                'label'   => $carrierTitle,
                'value' => array(),
            );
            foreach ($carrierMethods as $methodCode=>$methodTitle) {
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode.'_'.$methodCode,
                    'label' => '['.$carrierCode.'] '.$methodTitle,
                );
            }
        }

        return $methods;
    }
}
