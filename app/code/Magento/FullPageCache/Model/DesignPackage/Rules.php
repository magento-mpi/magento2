<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\DesignPackage;

class Rules
{
    /**
     * Design change cache suffix
     */
    const DESIGN_CHANGE_CACHE_SUFFIX    = 'FPC_DESIGN_CHANGE_CACHE';

    /**
     * Design change model
     *
     * @var \Magento\Core\Model\Design
     */
    protected $_designChange;

    /**
     * Design model
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design;

    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * @param \Magento\Core\Model\Design $designChange
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     */
    public function __construct(
        \Magento\Core\Model\Design $designChange,
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\FullPageCache\Model\Cache $fpcCache
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
            $output = \Magento\Core\Model\View\Design::getPackageByUserAgent($rules);
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

        $exceptions = $this->_fpcCache->load(\Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY);

        $date = date('Y-m-d');
        $changeCacheId =  $this->getCacheId($storeId, $date);
        $result = $this->_fpcCache->load($changeCacheId);
        if ($result === false) {
            $result = $this->_designChange->getResource()->loadChange($storeId, $date);
            $result = $result ?: array();
            $this->_fpcCache->save(
                serialize($result),
                $changeCacheId,
                array(\Magento\FullPageCache\Model\Processor::CACHE_TAG),
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
