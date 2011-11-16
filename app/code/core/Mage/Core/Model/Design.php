<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Design settings change model
 *
 * @method Mage_Core_Model_Resource_Design _getResource()
 * @method Mage_Core_Model_Resource_Design getResource()
 * @method int getStoreId()
 * @method Mage_Core_Model_Design setStoreId(int $value)
 * @method string getDesign()
 * @method Mage_Core_Model_Design setDesign(string $value)
 * @method string getDateFrom()
 * @method Mage_Core_Model_Design setDateFrom(string $value)
 * @method string getDateTo()
 * @method Mage_Core_Model_Design setDateTo(string $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Design extends Mage_Core_Model_Abstract
{
    const CACHE_TAG              = 'CORE_DESIGN';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string || true
     */
    protected $_cacheTag         = self::CACHE_TAG;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Design');
    }

    /**
     * Load custom design settings for specified store and date
     *
     * @param string $storeId
     * @param string|null $date
     * @return Mage_Core_Model_Design
     */
    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            $date = Varien_Date::formatDate(Mage::app()->getLocale()->storeTimeStamp($storeId), false);
        }

        $changeCacheId = 'design_change_' . md5($storeId . $date);
        $result = Mage::app()->loadCache($changeCacheId);
        if ($result === false) {
            $result = $this->getResource()->loadChange($storeId, $date);
            if (!$result) {
                $result = array();
            }
            Mage::app()->saveCache(serialize($result), $changeCacheId, array(self::CACHE_TAG), 86400);
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
     * @param Mage_Core_Model_Design_Package $packageInto
     * @return Mage_Core_Model_Design
     */
    public function changeDesign(Mage_Core_Model_Design_Package $packageInto)
    {
        if ($this->getDesign()) {
            $packageInto->setDesignTheme($this->getDesign());
        }
        return $this;
    }
}
