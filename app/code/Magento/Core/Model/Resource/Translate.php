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
 * Translation resource model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource;

class Translate extends \Magento\Core\Model\Resource\Db\AbstractDb implements \Magento\Translate\ResourceInterface
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($resource);
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
    }


    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core_translate', 'key_id');
    }

    /**
     * Retrieve translation array for store / locale code
     *
     * @param int $storeId
     * @param string $locale
     * @return array
     */
    public function getTranslationArray($storeId = null, $locale = null)
    {
        if (!$this->_appState->isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = $this->_getStoreId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return array();
        }

        $select = $adapter->select()
            ->from($this->getMainTable(), array('string', 'translate'))
            ->where('store_id IN (0 , :store_id)')
            ->where('locale = :locale')
            ->order('store_id');

        $bind = array(
            ':locale'   => (string)$locale,
            ':store_id' => $storeId
        );

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve translations array by strings
     *
     * @param array $strings
     * @param int_type $storeId
     * @return array
     */
    public function getTranslationArrayByStrings(array $strings, $storeId = null)
    {
        if (!$this->_appState->isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $this->_getStoreId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return array();
        }

        if (empty($strings)) {
            return array();
        }

        $bind = array(
            ':store_id'   => $storeId
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('string', 'translate'))
            ->where('string IN (?)', $strings)
            ->where('store_id = :store_id');

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve table checksum
     *
     * @return int
     */
    public function getMainChecksum()
    {
        return $this->getChecksum($this->getMainTable());
    }

    /**
     * Get store id for translations
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
