<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer\PlaceOrder\Restriction;

class Frontend implements \Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Reward\Helper\Data $helper
     */
    public function __construct(\Magento\Reward\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isEnabledOnFront();
    }
}
