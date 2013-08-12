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
class Magento_Backend_Model_Menu_Builder_Command_Add extends Magento_Backend_Model_Menu_Builder_CommandAbstract
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
     * @param Magento_Backend_Model_Menu_Builder_CommandAbstract $command
     * @return Magento_Backend_Model_Menu_Builder_CommandAbstract
     * @throws InvalidArgumentException
     */
    public function chain(Magento_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        if ($command instanceof Magento_Backend_Model_Menu_Builder_Command_Add) {
            throw new InvalidArgumentException("Two 'add' commands cannot have equal id (" . $command->getId() . ")");
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
