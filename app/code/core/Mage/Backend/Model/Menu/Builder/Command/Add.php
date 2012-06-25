<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Builder command to add menu items
 */
class Mage_Backend_Model_Menu_Builder_Command_Add extends Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    /**
     * List of params that command requires for execution
     *
     * @var array
     */
    protected $_requiredParams = array(
        "id",
        "title",
        "module"
    );

    /**
     * Add command as last in the list of callbacks
     *
     * @param Mage_Backend_Model_Menu_Builder_CommandAbstract $command
     * @return Mage_Backend_Model_Menu_Builder_CommandAbstract
     * @throws InvalidArgumentException
     */
    public function chain(Mage_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        if ($command instanceof Mage_Backend_Model_Menu_Builder_Command_Add) {
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
        foreach($this->_data as $key => $value) {
            $itemParams[$key] = isset($itemParams[$key]) ? $itemParams[$key] : $value;
        }
        return $itemParams;
    }
}
