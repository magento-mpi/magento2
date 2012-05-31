<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Simplexml extends Mage_Backend_Model_Menu_BuilderAbstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_factory;

    /**
     * Root menu
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    public function __construct(array $data = array())
    {
        if (!isset($data['factory'])) {
            throw new InvalidArgumentException();
        }
        $this->_factory = $data['factory'];

        if (!isset($data['menu']) || !($data['menu'] instanceof Mage_Backend_Model_Menu)) {
            throw new InvalidArgumentException();
        }
        $this->_menu = $data['menu'];
    }

    /**
     * @return Mage_Backend_Model_Menu
     */
    public function getResult()
    {
        /** @var $items Mage_Backend_Model_Menu_Item[] */
        $items = array();
        foreach ($this->_commands as $command) {
            $item = $command->execute($this->_factory->getModelInstance('Mage_Backend_Model_Menu_Item'));
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
