<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu builder command
 */
namespace Magento\Backend\Model\Menu\Builder;

abstract class CommandAbstract
{
    /**
     * List of required params
     *
     * @var array
     */
    protected $_requiredParams = array("id");

    /**
     * Command params array
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Next command in the chain
     *
     * @var \Magento\Backend\Model\Menu\Builder\CommandAbstract
     */
    protected $_next = null;

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        foreach ($this->_requiredParams as $param) {
            if (!isset($data[$param]) || is_null($data[$param])) {
                throw new \InvalidArgumentException("Missing required param " . $param);
            }
        }
        $this->_data = $data;
    }

    /**
     * Retreive id of element to apply command to
     *
     * @return int
     */
    public function getId()
    {
        return $this->_data['id'];
    }

    /**
     * Add command as last in the list of callbacks
     *
     * @param \Magento\Backend\Model\Menu\Builder\CommandAbstract $command
     * @return \Magento\Backend\Model\Menu\Builder\CommandAbstract
     * @throws \InvalidArgumentException if invalid chaining command is supplied
     */
    public function chain(\Magento\Backend\Model\Menu\Builder\CommandAbstract $command)
    {
        if (is_null($this->_next)) {
            $this->_next = $command;
        } else {
            $this->_next->chain($command);
        }
        return $this;
    }

    /**
     * Execute command and pass control to chained commands
     *
     * @param array $itemParams
     * @return array
     */
    public function execute(array $itemParams = array())
    {
        $itemParams = $this->_execute($itemParams);
        if (!is_null($this->_next)) {
            $itemParams = $this->_next->execute($itemParams);
        }
        return $itemParams;
    }

    /**
     * Execute internal command actions
     *
     * @param array $itemParams
     * @return array
     */
    protected abstract function _execute(array $itemParams);
}
