<?php
/**
 * Backend area front name resolver. Reads front name from configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Area;

class FrontNameResolver implements \Magento\Framework\App\Area\FrontNameResolverInterface
{
    const XML_PATH_USE_CUSTOM_ADMIN_PATH = 'admin/url/use_custom_path';

    const XML_PATH_CUSTOM_ADMIN_PATH = 'admin/url/custom_path';

    const PARAM_BACKEND_FRONT_NAME = 'backend/frontName';

    /**
     * Backend area code
     */
    const AREA_CODE = 'adminhtml';

    /**
     * @var string
     */
    protected $_defaultFrontName;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Backend\App\Config $config
     * @param string $defaultFrontName
     */
    public function __construct(\Magento\Backend\App\Config $config, $defaultFrontName)
    {
        $this->_config = $config;
        $this->_defaultFrontName = $defaultFrontName;
    }

    /**
     * Retrieve area front name
     *
     * @return string
     */
    public function getFrontName()
    {
        $isCustomPathUsed = (bool)(string)$this->_config->getValue(self::XML_PATH_USE_CUSTOM_ADMIN_PATH);
        if ($isCustomPathUsed) {
            return (string)$this->_config->getValue(self::XML_PATH_CUSTOM_ADMIN_PATH);
        }
        return $this->_defaultFrontName;
    }
}
