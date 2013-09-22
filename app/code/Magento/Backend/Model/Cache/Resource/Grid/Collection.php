<?php
/**
 * Cache grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Cache\Resource\Grid;

class Collection extends \Magento\Data\Collection
{
    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList
     * @param Magento_Core_Model_EntityFactory $entityFactory
     */
    public function __construct(
        Magento_Core_Model_Cache_TypeListInterface $cacheTypeList,
        Magento_Core_Model_EntityFactory $entityFactory
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Backend\Model\Cache\Resource\Grid\Collection
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
