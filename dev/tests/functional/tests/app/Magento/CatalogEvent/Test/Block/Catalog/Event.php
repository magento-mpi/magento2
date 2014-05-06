<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Test\Block\Catalog;

use Mtf\Block\Block;

/**
 * Class Event
 * Event block on the product/category pages
 *
 * @package Magento\CatalogEvent\Test\Block\Catalog
 */
class Event extends Block
{

    /**
     * Event block on the Frontend
     *
     * @var string
     */
    protected $eventStatus = '.subtitle';

    /**
     * Event Block
     *
     * @var string
     */
    protected $eventBlock = '.content';

    /**
     * Get Event Message
     */
    public function getEventMessage()
    {
        return $this->_rootElement->find($this->eventStatus)->getText();
    }
}
