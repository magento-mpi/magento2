<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer\PlaceOrder\Restriction;

class Backend
    implements \Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface
{
    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Reward\Helper\Data $helper
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Reward\Helper\Data $helper,
        \Magento\AuthorizationInterface $authorization
    ) {
        $this->_helper = $helper;
        $this->_authorization = $authorization;
    }

    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isEnabledOnFront()
            && $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_AFFECT);
    }
}
