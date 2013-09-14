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
class Magento_Sales_Model_Order_Config extends Magento_Core_Model_Config_Base
{
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
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Sales_Model_Order_StatusFactory $orderStatusFactory
     * @param Magento_Sales_Model_Resource_Order_Status_CollectionFactory $orderStatusCollFactory
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Sales_Model_Order_StatusFactory $orderStatusFactory,
        Magento_Sales_Model_Resource_Order_Status_CollectionFactory $orderStatusCollFactory,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
        parent::__construct($this->_coreConfig->getNode('global/sales/order'));
        $this->_orderStatusFactory = $orderStatusFactory;
        $this->_orderStatusCollFactory = $orderStatusCollFactory;
    }

    /**
     * @param string $status
     * @return Magento_Simplexml_Element
     */
    protected function _getStatus($status)
    {
        return $this->getNode('statuses/' . $status);
    }

    /**
     * @param string $state
     * @return Magento_Simplexml_Element
     */
    protected function _getState($state)
    {
        return $this->getNode('states/' . $state);
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
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $state = (string)$stateNode->label;
            return __($state);
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
        foreach ($this->getNode('states')->children() as $state) {
            $label = (string) $state->label;
            $states[$state->getName()] = __($label);
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
        if (is_array($state)) {
            $key = implode("|", $state) . $addLabels;
        } else {
            $key = $state . $addLabels;
        }
        if (isset($this->_stateStatuses[$key])) {
            return $this->_stateStatuses[$key];
        }
        $statuses = array();
        if (empty($state) || !is_array($state)) {
            $state = array($state);
        }
        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = $this->_orderStatusCollFactory->create()
                    ->addStateFilter($_state)
                    ->orderByLabel();
                foreach ($collection as $status) {
                    $code = $status->getStatus();
                    if ($addLabels) {
                        $statuses[$code] = $status->getStoreLabel();
                    } else {
                        $statuses[] = $code;
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
        $this->_getStates();
        return $this->_states['visible'];
    }

    /**
     * Get order states, visible on frontend
     *
     * @return array
     */
    public function getInvisibleOnFrontStates()
    {
        $this->_getStates();
        return $this->_states['invisible'];
    }

    private function _getStates()
    {
        if (null === $this->_states) {
            $this->_states = array(
                'all'       => array(),
                'visible'   => array(),
                'invisible' => array(),
                'statuses'  => array(),
            );
            foreach ($this->getNode('states')->children() as $state) {
                $name = $state->getName();
                $this->_states['all'][] = $name;
                $isVisibleOnFront = (string)$state->visible_on_front;
                if ((bool)$isVisibleOnFront || ($state->visible_on_front && $isVisibleOnFront == '')) {
                    $this->_states['visible'][] = $name;
                } else {
                    $this->_states['invisible'][] = $name;
                }
                foreach ($state->statuses->children() as $status) {
                    $this->_states['statuses'][$name][] = $status->getName();
                }
            }
        }
    }
}
