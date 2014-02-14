<?php
/**
 * Application area list
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class AreaList
{
    /**
     * Area configuration list
     *
     * @var array
     */
    protected $_areas;

    /**
     * @var string
     */
    protected $_defaultAreaCode;

    /**
     * @var Area\FrontNameResolverFactory
     */
    protected $_resolverFactory;

    /**
     * @param Area\FrontNameResolverFactory $resolverFactory
     * @param array $areas
     * @param string $default
     */
    public function __construct(Area\FrontNameResolverFactory $resolverFactory, array $areas, $default)
    {
        $this->_resolverFactory = $resolverFactory;
        $this->_areas = $areas;
        $this->_defaultAreaCode = $default;
    }

    /**
     * Retrieve area code by front name
     *
     * @param string $frontName
     * @return null|string
     */
    public function getCodeByFrontName($frontName)
    {
        foreach ($this->_areas as $areaCode => &$areaInfo) {
            if (!isset($areaInfo['frontName']) && isset($areaInfo['frontNameResolver'])) {
                $areaInfo['frontName'] = $this->_resolverFactory->create($areaInfo['frontNameResolver'])
                    ->getFrontName();
            }
            if ($areaInfo['frontName'] == $frontName) {
                return $areaCode;
            }
        }
        return $this->_defaultAreaCode;
    }

    /**
     * Retrieve area front name by code
     *
     * @param string $areaCode
     * @return string
     */
    public function getFrontName($areaCode)
    {
        return isset($this->_areas[$areaCode]['frontName']) ? $this->_areas[$areaCode]['frontName'] : null;
    }

    /**
     * Retrieve area codes
     *
     * @return string[]
     */
    public function getCodes()
    {
        return array_keys($this->_areas);
    }

    /**
     * Retrieve default area router id
     *
     * @param string $areaCode
     * @return string
     */
    public function getDefaultRouter($areaCode)
    {
        return isset($this->_areas[$areaCode]['router']) ? $this->_areas[$areaCode]['router'] : null;
    }
}