<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
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
     */
    public function getResult()
    {
        /** @var $items Mage_Backend_Model_Menu_Item[] */
        $items = array();
        foreach ($this->_commands as $command) {
            $item = $this->_itemFactory->createFromArray($command->execute(array()));
            $items[$item->getId()] = $item;
        }

        foreach($items as $item) {
            if (!$item->hasParent()) {
                $this->_menu->addChild($item);
            } else {
                $items[$item->getParent()]->addChild($item);
            }
        }
        return $this->_menu;
    }
}
