<?php
/**
 * Created by PhpStorm.
 * User: oonoshko
 * Date: 17.04.14
 * Time: 18:47
 */

namespace Magento\CatalogEvent\Test\Block;

use Mtf\Block\Block;

class Event extends Block
{

    /**
     * Event block on the Frontend
     *
     * @var string
     */
    protected $eventStatus = '.subtitle';

    /**
     * button Add to Card on the Product Page
     *
     * @var string
     */
    protected $addToCartOnProduct = '#product-addtocart-button';

    /**
     * button Event Block
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

    /**
     * Get Event block on Product Page
     */
    public function getAddToCartButtonOnProduct()
    {
        return $this->_rootElement->find($this->addToCartOnProduct)->isVisible();
    }

    /**
     * Get Event block
     */
    public function getEventBlock()
    {
        return $this->_rootElement->find($this->eventBlock)->isVisible();
    }

}
