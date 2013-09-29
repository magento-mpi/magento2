<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order configuration model
 */
class Magento_Sales_Model_Order_Config
{
    /**
     * @var Magento_Sales_Model_Resource_Order_Status_Collection
     */
    protected $_collection;

    /**
     * Statuses per state array
     *
     * @var array
     */
    protected $_stateStatuses;

    /**
     * @var array
     */
    private $_states;

    /**
     * @var Magento_Sales_Model_Order_Status
     */
    protected $_orderStatusFactory;

    /**
     * @var Magento_Sales_Model_Resource_Order_Status_CollectionFactory
     */
    protected $_orderStatusCollFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Sales_Model_Order_StatusFactory $orderStatusFactory,
        Magento_Sales_Model_Resource_Order_Status_CollectionFactory $orderStatusCollFactory
    ) {
        $this->_orderStatusFactory = $orderStatusFactory;
        $this->_orderStatusCollFactory = $orderStatusCollFactory;
    }

    /**
     * @return Magento_Sales_Model_Resource_Order_Status_Collection
     */
    protected function _getCollection()
    {
        if ($this->_collection == null) {
            $this->_collection = $this->_orderStatusCollFactory->create()->joinStates();
        }
        return $this->_collection;
    }

    /**
     * @param string $state
     * @return Magento_Sales_Model_Order_Status
     */
    protected function _getState($state)
    {
        foreach ($this->_getCollection() as $item) {
            if ($item->getData('state') == $state) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Retrieve default status for state
     *
     * @param   string $state
     * @return  string
     */
    public function getStateDefaultStatus($state)
    {
        $status = false;
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $status = $this->_orderStatusFactory->create()->loadDefaultByState($state);
            $status = $status->getStatus();
        }
        return $status;
    }

    /**
     * Retrieve status label
     *
     * @param   string $code
     * @return  string
     */
    public function getStatusLabel($code)
    {
        $status = $this->_orderStatusFactory->create()->load($code);
        return $status->getStoreLabel();
    }

    /**
     * State label getter
     *
     * @param   string $state
     * @return  string
     */
    public function getStateLabel($state)
    {
        if ($stateItem = $this->_getState($state)) {
            $label = $stateItem->getData('label');
            return __($label);
        }
        return $state;
    }


    /**
     * Retrieve all statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        $statuses = $this->_orderStatusCollFactory->create()->toOptionHash();
        return $statuses;
    }

    /**
     * Order states getter
     *
     * @return array
     */
    public function getStates()
    {
        $states = array();
        foreach ($this->_getCollection() as $item) {
            $states[$item->getState()] = __($item->getData('label'));
        }
        return $states;
    }


    /**
     * Retrieve statuses available for state
     * Get all possible statuses, or for specified state, or specified states array
     * Add labels by default. Return plain array of statuses, if no labels.
     *
     * @param mixed $state
     * @param bool $addLabels
     * @return array
     */
    public function getStateStatuses($state, $addLabels = true)
    {
        $key = md5(serialize(array($state, $addLabels)));
        if (isset($this->_stateStatuses[$key])) {
            return $this->_stateStatuses[$key];
        }
        $statuses = array();

        if (!is_array($state)) {
            $state = array($state);
        }
        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = $this->_orderStatusCollFactory->create()
                    ->addStateFilter($_state)
                    ->orderByLabel();
                foreach ($collection as $item) {
                    $status = $item->getData('status');
                    if ($addLabels) {
                        $statuses[$status] = $item->getStoreLabel();
                    } else {
                        $statuses[] = $status;
                    }
                }
            }
        }
        $this->_stateStatuses[$key] = $statuses;
        return $statuses;
    }

    /**
     * Retrieve states which are visible on front end
     *
     * @return array
     */
    public function getVisibleOnFrontStates()
    {
        return $this->_getStates(true);
    }

    /**
     * Get order states, visible on frontend
     *
     * @return array
     */
    public function getInvisibleOnFrontStates()
    {
        return $this->_getStates(false);
    }

    /**
     * @param bool $visibility
     *
     * @return array
     */
    protected function _getStates($visibility)
    {
        $visibility = (bool)$visibility;
        if ($this->_states == null) {
            foreach($this->_getCollection() as $item) {
                $visible = (bool)$item->getData('visible_on_front');
                $this->_states[$visible][] = $item->getData('state');
            }
        }
        return  $this->_states[$visibility];
    }
}
