<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Config;

class StructurePlugin
{
    /**
     * Request parameter name
     */
    const REQUEST_PARAM_COUNTRY = 'paypal_country';

    /**
     * @var \Magento\Paypal\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Backend\Model\Config\ScopeDefiner
     */
    protected $_scopeDefiner;

    /**
     * @param \Magento\Backend\Model\Config\ScopeDefiner $scopeDefiner
     * @param \Magento\Paypal\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Model\Config\ScopeDefiner $scopeDefiner,
        \Magento\Paypal\Helper\Backend $helper
    ) {
        $this->_scopeDefiner = $scopeDefiner;
        $this->_helper = $helper;
    }

    /**
     * Substitute payment section with PayPal configs
     *
     * @param \Magento\Backend\Model\Config\Structure $subject
     * @param \Closure $proceed
     * @param array $pathParts
     * @return \Magento\Backend\Model\Config\Structure\ElementInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetElementByPathParts(
        \Magento\Backend\Model\Config\Structure $subject,
        \Closure $proceed,
        array $pathParts
    ) {
        $isSectionChanged = $pathParts[0] == 'payment';
        if ($isSectionChanged) {
            $requestedCountrySection = 'payment_' . strtolower($this->_helper->getConfigurationCountryCode());
            if (in_array(
                $requestedCountrySection,
                [
                    'payment_us',
                    'payment_ca',
                    'payment_au',
                    'payment_gb',
                    'payment_jp',
                    'payment_fr',
                    'payment_it',
                    'payment_es',
                    'payment_hk',
                    'payment_nz',
                    'payment_de'
                ]
            )) {
                $pathParts[0] = $requestedCountrySection;
            } else {
                $pathParts[0] = 'payment_other';
            }
        }
        /** @var \Magento\Backend\Model\Config\Structure\ElementInterface $result */
        $result = $proceed($pathParts);
        if ($isSectionChanged && isset($result)) {
            $result->setData(array_merge(
                $result->getData(),
                ['showInDefault' => true, 'showInWebsite' => true, 'showInStore' => true]
            ), $this->_scopeDefiner->getScope());
        }
        return $result;
    }
}
