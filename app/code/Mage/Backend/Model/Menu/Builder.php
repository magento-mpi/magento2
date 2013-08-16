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
 * Menu builder object. Retrieves commands (Mage_Backend_Model_Menu_Builder_CommandAbstract)
 * to build menu (Mage_Backend_Model_Menu)
 */
class Mage_Backend_Model_Menu_Builder
{
    /**
     * @var Mage_Backend_Model_Menu_Builder_CommandAbstract[]
     */
    protected $_commands = array();

    /**
     * @var Mage_Backend_Model_Menu_Item_Factory
     */
    protected $_itemFactory;

    /**
     * @param Mage_Backend_Model_Menu_Item_Factory $menuItemFactory
     */
    public function __construct(
        Mage_Backend_Model_Menu_Item_Factory $menuItemFactory
    ) {
        $this->_itemFactory = $menuItemFactory;
    }

    /**
     * Process provided command object
     *
     * @param Mage_Backend_Model_Menu_Builder_CommandAbstract $command
     * @return Mage_Backend_Model_Menu_Builder
     */
    public function processCommand(Mage_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        if (!isset($this->_commands[$command->getId()])) {
            $this->_commands[$command->getId()] = $command;
        } else {
            $this->_commands[$command->getId()]->chain($command);
        }
        return $this;
    }

    /**
     * Populate menu object
     *
     * @param Mage_Backend_Model_Menu $menu
     * @return Mage_Backend_Model_Menu
     * @throws OutOfRangeException in case given parent id does not exists
     */
    public function getResult(Mage_Backend_Model_Menu $menu)
    {
        /** @var $items Mage_Backend_Model_Menu_Item[] */
        $params = array();
        $items = array();

        // Create menu items
        foreach ($this->_commands as $id => $command) {
            $params[$id] = $command->execute();
            $item = $this->_itemFactory->create($params[$id]);
            $items[$id] = $item;
        }

        // Build menu tree based on "parent" param
        foreach ($items as $id => $item) {
            $sortOrder = $this->_getParam($params[$id], 'sortOrder');
            $parentId = $this->_getParam($params[$id], 'parent');
            $isRemoved = isset($params[$id]['removed']);

            if ($isRemoved) {
                continue;
            }
            if (!$parentId) {
                $menu->add($item, null, $sortOrder);
            } else {
                if (!isset($items[$parentId])) {
                    throw new OutOfRangeException(sprintf('Specified invalid parent id (%s)', $parentId));
                }
                if (isset($params[$parentId]['removed'])) {
                    continue;
                }
                $items[$parentId]->getChildren()->add($item, null, $sortOrder);
            }
        }

        return $menu;
    }

    /**
     * Retrieve param by name or default value
     *
     * @param array $params
     * @param string $paramName
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParam($params, $paramName, $defaultValue = null)
    {
        return isset($params[$paramName]) ? $params[$paramName] : $defaultValue;
    }
}
