<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translation\Model\Resource;

class Translate extends \Magento\Model\Resource\Db\AbstractDb implements \Magento\Translate\ResourceInterface
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var null|string
     */
    protected $scope;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\App\State $appState
     * @param \Magento\App\ScopeResolverInterface $scopeResolver
     * @param null|string $scope
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\App\State $appState,
        \Magento\App\ScopeResolverInterface $scopeResolver,
        $scope = null
    ) {
        $this->_appState = $appState;
        $this->scopeResolver = $scopeResolver;
        $this->scope = $scope;
        parent::__construct($resource);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('translation', 'key_id');
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
            $storeId = $this->getStoreId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return array();
        }

        $select = $adapter->select()->from(
            $this->getMainTable(),
            array('string', 'translate')
        )->where(
            'store_id IN (0 , :store_id)'
        )->where(
            'locale = :locale'
        )->order(
            'store_id'
        );

        $bind = array(':locale' => (string)$locale, ':store_id' => $storeId);

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve translations array by strings
     *
     * @param array $strings
     * @param int|null $storeId
     * @return array
     */
    public function getTranslationArrayByStrings(array $strings, $storeId = null)
    {
        if (!$this->_appState->isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return array();
        }

        if (empty($strings)) {
            return array();
        }

        $bind = array(':store_id' => $storeId);
        $select = $adapter->select()->from(
            $this->getMainTable(),
            array('string', 'translate')
        )->where(
            'string IN (?)',
            $strings
        )->where(
            'store_id = :store_id'
        );

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
     * Retrieve current store identifier
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->scopeResolver->getScope($this->scope)->getId();
    }
}
