<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

use Magento\Core\Model\Resource\AbstractResource;

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
 */
class Design extends AbstractModel implements \Magento\Object\IdentityInterface
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
     * @var LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param LocaleInterface $locale
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Stdlib\DateTime $dateTime,
        AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Design');
    }

    /**
     * Load custom design settings for specified store and date
     *
     * @param string $storeId
     * @param string|null $date
     * @return $this
     */
    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            $date = $this->_dateTime->formatDate($this->_locale->storeTimeStamp($storeId), false);
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
     * @param \Magento\View\DesignInterface $packageInto
     * @return $this
     */
    public function changeDesign(\Magento\View\DesignInterface $packageInto)
    {
        $design = $this->getDesign();
        if ($design) {
            $packageInto->setDesignTheme($design);
        }
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
