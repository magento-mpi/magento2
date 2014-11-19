<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Code\Generator;

use Magento\Framework\ObjectManager\Config;
use Magento\Tools\Di\Code\Scanner;
use Magento\Framework\Interception\Config\Config as InterceptionConfig;

class InterceptorGenerator
{
    /**
     * Area code list: global, frontend, etc.
     *
     * @var array
     */
    private $areaCodesList = [];

    /**
     * Virtual types specific to area: [areaCode => [virtual types]]
     *
     * @var array
     */
    private $areaVirtualTypes = [];

    /**
     * @var InterceptionConfig
     */
    private $interceptionConfig;

    /**
     * @param InterceptionConfig $interceptionConfig
     */
    public function __construct(InterceptionConfig $interceptionConfig)
    {
        $this->interceptionConfig = $interceptionConfig;
    }


    /**
     * Adds area code
     *
     * @param string $areaCode
     * @return void
     */
    public function addAreaCode($areaCode)
    {
        if (empty($this->areaCodesList[$areaCode])) {
            $this->areaCodesList[] = $areaCode;
        }
    }

    /**
     * Adds virtual types for specific area
     *
     * @param string $areaCode
     * @param Config $areaConfig
     * @return void
     */
    public function addAreaConfig($areaCode, Config $areaConfig)
    {
        if (empty($this->areaVirtualTypes[$areaCode])) {
            $this->areaVirtualTypes[$areaCode] = $areaConfig;
        }
    }

    public function generate()
    {
        $definedClasses = get_declared_classes();
        $intercepted = [];
        $magentoClasses = array_filter($definedClasses, function($s){return strpos($s, 'Magento') !== false;});
        foreach ($definedClasses as $definedClass) {
            if ($this->interceptionConfig->hasPlugins($definedClass)) {
                $intercepted[] = $definedClass;
            }
        }
        return $intercepted;
    }

    private function getInterceptionConfiguration($areaCode)
    {

    }

    private function resolveVirtualTypes($interceptionConfiguration, $virtualTypes)
    {

    }

    private function getInterceptedMethods($interceptorConfiguration)
    {

    }

    private function generateInterceptor($interceptorConfiguration)
    {

    }
}
