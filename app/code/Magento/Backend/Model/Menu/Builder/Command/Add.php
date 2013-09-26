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
 * Builder command to add menu items
 */
namespace Magento\Backend\Model\Menu\Builder\Command;

class Add extends \Magento\Backend\Model\Menu\Builder\AbstractCommand
{
    /**
     * List of params that command requires for execution
     *
     * @var array
     */
    protected $_requiredParams = array(
        "id",
        "title",
        "module",
        "resource"
    );

    /**
     * Add command as last in the list of callbacks
     *
     * @param \Magento\Backend\Model\Menu\Builder\AbstractCommand $command
     * @return \Magento\Backend\Model\Menu\Builder\AbstractCommand
     * @throws \InvalidArgumentException
     */
    public function chain(\Magento\Backend\Model\Menu\Builder\AbstractCommand $command)
    {
        if ($command instanceof \Magento\Backend\Model\Menu\Builder\Command\Add) {
            throw new \InvalidArgumentException("Two 'add' commands cannot have equal id (" . $command->getId() . ")");
        }
        return parent::chain($command);
    }

    /**
     * Add missing data to item
     *
     * @param array $itemParams
     * @return array
     */
    protected function _execute(array $itemParams)
    {
        foreach ($this->_data as $key => $value) {
            $itemParams[$key] = isset($itemParams[$key]) ? $itemParams[$key] : $value;
        }
        return $itemParams;
    }
}
