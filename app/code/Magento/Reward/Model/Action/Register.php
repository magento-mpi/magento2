<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action for new customer registration
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

class Register extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param array $data
     */
    public function __construct(\Magento\Reward\Helper\Data $rewardData, array $data = array())
    {
        $this->_rewardData = $rewardData;
        parent::__construct($data);
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)$this->_rewardData->getPointsConfig('register', $websiteId);
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        return __('Registered as customer');
    }
}
