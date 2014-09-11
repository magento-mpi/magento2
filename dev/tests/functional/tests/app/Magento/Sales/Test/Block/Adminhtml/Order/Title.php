<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;

/**
 * Class Title
 * Title block on order entities view page
 */
class Title extends Block
{
    /**
     * Entity id css selector
     *
     * @var string
     */
    protected $entityId = 'h1.title';

    /**
     * Get entity id
     *
     * @return int
     */
    public function getId()
    {
        return trim($this->_rootElement->find($this->entityId)->getText(), ' #');
    }
}
