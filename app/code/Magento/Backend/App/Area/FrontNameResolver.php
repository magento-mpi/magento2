<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Area;

class FrontNameResolver implements \Magento\App\Area\FrontNameResolverInterface
{
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'admin/url/custom_path';

    const PARAM_BACKEND_FRONT_NAME              = 'backend.frontName';


    /**
     * Area front name
     *
     * @var string
     */
    protected $_areaFrontName;

    /**
     * @var string
     */
    protected $_defaultFrontName;

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\ConfigInterface $config
     * @param string $defaultFrontName
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $config, $defaultFrontName)
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
        if (null === $this->_areaFrontName) {
            $isCustomPathUsed = (bool)(string)$this->_config->getValue(self::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default');

            if ($isCustomPathUsed) {
                $this->_areaFrontName = (string)$this->_config->getValue(self::XML_PATH_CUSTOM_ADMIN_PATH, 'default');
            } else {
                $this->_areaFrontName = $this->_defaultFrontName;
            }
        }
        return $this->_areaFrontName;
    }
}