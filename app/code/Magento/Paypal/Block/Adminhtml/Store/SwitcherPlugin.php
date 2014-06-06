<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Store;

class SwitcherPlugin
{
    /**
     * Remove country request param from url
     *
     * @param \Magento\Backend\Block\Store\Switcher $subject
     * @param \Closure $proceed
     * @param string $route
     * @param array $params
     * @return string
     */
    public function aroundGetUrl(
        \Magento\Backend\Block\Store\Switcher $subject,
        \Closure $proceed,
        $route = '',
        $params = array()
    ) {
        if ($subject->getRequest()->getParam(\Magento\Paypal\Model\Config\StructurePlugin::REQUEST_PARAM_COUNTRY)) {
            $params[\Magento\Paypal\Model\Config\StructurePlugin::REQUEST_PARAM_COUNTRY] = null;
        }
        return $proceed($route, $params);
    }
}
