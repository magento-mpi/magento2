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
     * Design model
     *
     * @var Mage_Core_Model_Design
     */
    protected $_design;

    /**
     * Design package model
     *
     * @var Mage_Core_Model_Design_Package
     */
    protected $_designPackage;

    /**
     * FPC cache model
     *
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * @param Mage_Core_Model_Design_Proxy $design
     * @param Mage_Core_Model_Design_Package_Proxy $designPackage
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     */
    public function __construct(
        Mage_Core_Model_Design_Proxy $design,
        Mage_Core_Model_Design_Package_Proxy $designPackage,
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
     * @return string
     */
    public function getPackageName()
    {
        $exceptions = $this->_fpcCache->load(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY);

        Magento_Profiler::start('process_design_change');
        //TODO:: need to identify current requested store view id
        $storeId = 1;
        $this->_design->loadChange($storeId, date('Y-m-d'));
        Magento_Profiler::stop('process_design_change');

        $output = $this->_getPackageByUserAgent($exceptions);
        if ('' === $output) {
            $output = $this->_design->getDesign();
        }

        return $output;
    }
}
