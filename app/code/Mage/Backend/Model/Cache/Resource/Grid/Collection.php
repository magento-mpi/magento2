<?php
/**
 * Cache grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Cache_Resource_Grid_Collection extends Magento_Data_Collection
{
    /**
     * @var Mage_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Mage_Core_Model_Cache_TypeListInterface $cacheTypeList
     */
    public function __construct(Mage_Core_Model_Cache_TypeListInterface $cacheTypeList)
    {
        $this->_cacheTypeList = $cacheTypeList;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Backend_Model_Cache_Resource_Grid_Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            foreach ($this->_cacheTypeList->getTypes() as $type) {
                $this->addItem($type);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
