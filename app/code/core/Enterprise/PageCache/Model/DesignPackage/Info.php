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
     * @param Enterprise_PageCache_Model_DesignPackage_Rules $packageRules
     */
    public function __construct(
        Enterprise_PageCache_Model_Cache $fpcCache,
        Enterprise_PageCache_Model_DesignPackage_Rules $packageRules
    ) {
        $exceptions = $fpcCache->load(self::DESIGN_EXCEPTION_KEY);
        $this->_designExceptionExistsInCache = $fpcCache->getFrontend()->test(self::DESIGN_EXCEPTION_KEY);
        $this->_packageName = $packageRules->getPackageByUserAgent($exceptions);
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
