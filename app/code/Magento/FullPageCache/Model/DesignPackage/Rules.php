<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_DesignPackage_Rules
{
    /**
     * Design change cache suffix
     */
    const DESIGN_CHANGE_CACHE_SUFFIX    = 'FPC_DESIGN_CHANGE_CACHE';

    /**
     * Design change model
     *
     * @var Magento_Core_Model_Design
     */
    protected $_designChange;

    /**
     * Design model
     *
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * FPC cache model
     *
     * @var Magento_FullPageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * @param Magento_Core_Model_Design $designChange
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     */
    public function __construct(
        Magento_Core_Model_Design $designChange,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_FullPageCache_Model_Cache $fpcCache
    ) {
        $this->_designChange = $designChange;
        $this->_design = $design;
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
            $output = Magento_Core_Model_View_Design::getPackageByUserAgent($rules);
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
        \Magento\Profiler::start('process_design_change');

        $exceptions = $this->_fpcCache->load(Magento_FullPageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY);

        $date = date('Y-m-d');
        $changeCacheId =  $this->getCacheId($storeId, $date);
        $result = $this->_fpcCache->load($changeCacheId);
        if ($result === false) {
            $result = $this->_designChange->getResource()->loadChange($storeId, $date);
            $result = $result ?: array();
            $this->_fpcCache->save(
                serialize($result),
                $changeCacheId,
                array(Magento_FullPageCache_Model_Processor::CACHE_TAG),
                86400
            );
        } else {
            $result = unserialize($result);
        }

        \Magento\Profiler::stop('process_design_change');

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
