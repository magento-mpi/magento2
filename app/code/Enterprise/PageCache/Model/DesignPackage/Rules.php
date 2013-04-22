<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_DesignPackage_Rules
{
    /**
     * Design change cache suffix
     */
    const DESIGN_CHANGE_CACHE_SUFFIX    = 'FPC_DESIGN_CHANGE_CACHE';

    /**
     * Design model
     *
     * @var Mage_Core_Model_Design
     */
    protected $_design;

    /**
     * Design package model
     *
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_designPackage;

    /**
     * FPC cache model
     *
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * @param Mage_Core_Model_Design $design
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     */
    public function __construct(
        Mage_Core_Model_Design $design,
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Enterprise_PageCache_Model_Cache $fpcCache
    ) {
        $this->_design = $design;
        $this->_designPackage = $designPackage;
        $this->_fpcCache = $fpcCache;
    }
    /**
     * Get package name based on design exception rules
     *
     * @param string $exceptions - design exception rules
     * @return string
     */
    protected function _getPackageByUserAgent($exceptions)
    {
        $output = '';
        $rules = $exceptions ? @unserialize($exceptions) : array();
        if (false === empty($rules)) {
            $output = Mage_Core_Model_Design_Package::getPackageByUserAgent($rules);
        }
        return $output;
    }

    /**
     * Get package name based on design exception rules and design change schedules
     *
     * @param int $storeId
     * @return string
     */
    public function getPackageName($storeId)
    {
        Magento_Profiler::start('process_design_change');

        $exceptions = $this->_fpcCache->load(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY);

        $date = date('Y-m-d');
        $changeCacheId =  $this->getCacheId($storeId, $date);
        $result = $this->_fpcCache->load($changeCacheId);
        if ($result === false) {
            $result = $this->_design->getResource()->loadChange($storeId, $date);
            $result = $result ?: array();
            $this->_fpcCache->save(
                serialize($result),
                $changeCacheId,
                array(Enterprise_PageCache_Model_Processor::CACHE_TAG),
                86400
            );
        } else {
            $result = unserialize($result);
        }

        Magento_Profiler::stop('process_design_change');

        $output = $this->_getPackageByUserAgent($exceptions);
        if ('' === $output) {
            $output = isset($result['design']) ? $result['design'] : '';
        }

        return $output;
    }

    /**
     * Get cache id
     *
     * @param int $storeId
     * @param string $date
     * @return string
     */
    public function getCacheId($storeId, $date = null)
    {
        $date = is_null($date) ? date('Y-m-d') : $date;
        return self::DESIGN_CHANGE_CACHE_SUFFIX . '_' . md5($storeId . $date);
    }
}
