<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model;

use Magento\CatalogInventory\Api\Data\StockInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Stock
 * @package Magento\CatalogInventory\Model
 * @data-api
 */
class Stock extends AbstractModel implements StockInterface
{
    /**
     * Stock entity code
     */
    const ENTITY = 'cataloginventory_stock';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $eventPrefix = 'cataloginventory_stock';

    /**
     * Parameter name in event
     * In observe method you can use $observer->getEvent()->getStock() in this case
     *
     * @var string
     */
    protected $eventObject = 'stock';

    const BACKORDERS_NO = 0;

    const BACKORDERS_YES_NONOTIFY = 1;

    const BACKORDERS_YES_NOTIFY = 2;

    const STOCK_OUT_OF_STOCK = 0;

    const STOCK_IN_STOCK = 1;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogInventory\Model\Resource\Stock');
    }

    /**
     * Retrieve stock identifier
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * Retrieve website identifier
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_getData(self::WEBSITE_ID);
    }

    /**
     * Retrieve Stock Name
     *
     * @return string
     */
    public function getStockName()
    {
        return $this->_getData(self::STOCK_NAME);
    }
}
