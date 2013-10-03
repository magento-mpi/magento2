<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\DesignPackage;

class Info
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
    protected $_designExceptionExistsInCache = null;

    /**
     * Design package name
     *
     * @var string
     */
    protected $_packageName = null;

    /**
     * Design package rules
     *
     * @var \Magento\FullPageCache\Model\DesignPackage\Rules
     */
    protected $_designPackageRules;

    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\DesignPackage\Rules $packageRules
     */
    public function __construct(
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\DesignPackage\Rules $packageRules
    ) {
        $this->_fpcCache = $fpcCache;
        $this->_designPackageRules = $packageRules;
    }

    /**
     * Return package name based on design exception rules
     *
     * @param int $storeId
     * @return null|string
     */
    public function getPackageName($storeId)
    {
        if (null === $this->_packageName) {
            $this->_packageName = $this->_designPackageRules->getPackageName($storeId);
        }
        return $this->_packageName;
    }

    /**
     * Check if design exception exists in cache
     *
     * @return bool|int
     */
    public function isDesignExceptionExistsInCache()
    {
        if (null === $this->_designExceptionExistsInCache) {
            $this->_designExceptionExistsInCache = $this->_fpcCache->getFrontend()->test(self::DESIGN_EXCEPTION_KEY);
        }

        return $this->_designExceptionExistsInCache;
    }
}
