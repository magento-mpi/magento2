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
     * Root menu
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        if (!isset($data['itemFactory']) || !($data['itemFactory'] instanceof Mage_Backend_Model_Menu_Item_Factory)) {
            throw new InvalidArgumentException('Wrong item factory provided');
        }
        $this->_itemFactory = $data['itemFactory'];

        if (!isset($data['menu']) || !($data['menu'] instanceof Mage_Backend_Model_Menu)) {
            throw new InvalidArgumentException();
        }
        $this->_menu = $data['menu'];
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
     * @return Mage_Backend_Model_Menu
     * @throws OutOfRangeException in case given parent id does not exists
     */
    public function getResult()
    {
        /** @var $items Mage_Backend_Model_Menu_Item[] */
        $params = array();
        $items = array();

        // Create menu items
        foreach ($this->_commands as $id => $command) {
            $params[$id] = $command->execute();
            $item = $this->_itemFactory->createFromArray($params[$id]);
            $items[$id] = $item;
        }

        // Build menu tree based on "parent" param
        foreach($items as $id => $item) {
            $sortOrder = isset($params[$id]['sortOrder']) ? $params[$id]['sortOrder'] : null;
            $parentId = isset($params[$id]['parent']) ? $params[$id]['parent'] : null;
            $isRemoved = isset($params[$id]['removed']);

            if ($isRemoved) {
                continue;
            }
            if (!$parentId) {
                $this->_menu->add($item, null, $sortOrder);
            } else {
                if (!isset($items[$parentId])) {
                    throw new OutOfRangeException(sprintf('Specified invalid parent id (%s)', $params[$id]['parent']));
                }
                if (isset($params[$parentId]['removed'])) {
                    continue;
                }
                $items[$parentId]->getChildren()->add($item, null, $sortOrder);
            }
        }

        return $this->_menu;
    }
}
