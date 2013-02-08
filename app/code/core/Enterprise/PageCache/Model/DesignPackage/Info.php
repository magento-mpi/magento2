<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_DesignPackage_Info
{
    /**
     * FPC design exception cache key
     */
    const DESIGN_EXCEPTION_KEY = 'FPC_DESIGN_EXCEPTION_CACHE';

    /**
     * Flag whether design exception value presents in cache
     * It always must be present (maybe serialized empty value)
     * @var boolean
     */
    protected $_designExceptionExistsInCache = false;

    /**
     * Design package name
     *
     * @var string
     */
    protected $_packageName = null;

    /**
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     */
    public function __construct(Enterprise_PageCache_Model_Cache $fpcCache)
    {
        $exceptions = $fpcCache->load(self::DESIGN_EXCEPTION_KEY);
        $this->_designExceptionExistsInCache = $fpcCache->getFrontend()->test(self::DESIGN_EXCEPTION_KEY);

        if ($exceptions) {
            $rules = @unserialize($exceptions);
            $this->_packageName = $this->_getPackageByUserAgent($rules);
        }
    }

    /**
     * Get package name based on design exception rules
     *
     * @param array $rules - design exception rules
     * @return null|string
     */
    protected function _getPackageByUserAgent($rules)
    {
        $output = null;
        if (false === empty($rules)) {
            $output = Mage_Core_Model_Design_Package::getPackageByUserAgent($rules);
        }
        return $output;
    }

    /**
     * Return package name based on design exception rules
     *
     * @return null|string
     */
    public function getPackageName()
    {
        return $this->_packageName;
    }

    /**
     * Check if design exception exists in cache
     *
     * @return bool|int
     */
    public function isDesignExceptionExistsInCache()
    {
        return $this->_designExceptionExistsInCache;
    }
}
