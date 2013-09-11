<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Design settings change model
 *
 * @method \Magento\Core\Model\Resource\Design _getResource()
 * @method \Magento\Core\Model\Resource\Design getResource()
 * @method int getStoreId()
 * @method \Magento\Core\Model\Design setStoreId(int $value)
 * @method string getDesign()
 * @method \Magento\Core\Model\Design setDesign(string $value)
 * @method string getDateFrom()
 * @method \Magento\Core\Model\Design setDateFrom(string $value)
 * @method string getDateTo()
 * @method \Magento\Core\Model\Design setDateTo(string $value)
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

class Design extends \Magento\Core\Model\AbstractModel
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'CORE_DESIGN';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_design';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string|bool
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_locale = $locale;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Core\Model\Resource\Design');
    }

    /**
     * Load custom design settings for specified store and date
     *
     * @param string $storeId
     * @param string|null $date
     * @return \Magento\Core\Model\Design
     */
    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            $date = \Magento\Date::formatDate($this->_locale->storeTimeStamp($storeId), false);
        }

        $changeCacheId = 'design_change_' . md5($storeId . $date);
        $result = $this->_cacheManager->load($changeCacheId);
        if ($result === false) {
            $result = $this->getResource()->loadChange($storeId, $date);
            if (!$result) {
                $result = array();
            }
            $this->_cacheManager->save(serialize($result), $changeCacheId, array(self::CACHE_TAG), 86400);
        } else {
            $result = unserialize($result);
        }

        if ($result) {
            $this->setData($result);
        }

        return $this;
    }

    /**
     * Apply design change from self data into specified design package instance
     *
     * @param \Magento\Core\Model\View\DesignInterface $packageInto
     * @return \Magento\Core\Model\Design
     */
    public function changeDesign(\Magento\Core\Model\View\DesignInterface $packageInto)
    {
        $design = $this->getDesign();
        if ($design) {
            $packageInto->setDesignTheme($design);
        }
        return $this;
    }
}
