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
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
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
