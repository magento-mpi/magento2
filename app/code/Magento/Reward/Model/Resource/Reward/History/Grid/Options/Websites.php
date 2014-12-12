<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Resource\Reward\History\Grid\Options;

use Magento\Reward\Model\Source\Website;

class Websites implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var \Magento\Reward\Model\Source\Website
     */
    protected $_systemStore;

    /**
     * @param Website $systemStore
     */
    public function __construct(Website $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->toOptionArray(false);
    }
}
