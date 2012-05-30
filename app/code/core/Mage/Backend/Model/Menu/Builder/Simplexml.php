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

    public function __construct(array $data = array())
    {
        if (!isset($data['factory'])) {
            throw new InvalidApplicationException();
        }
        $this->_factory = $data['factory'];

        if (!isset($data['root']) || !($data['root'] instanceof Varien_Simplexml_Element)) {
            throw new InvalidApplicationException();
        }
        $this->_root = $data['root'];
    }

    /**
     * @return Varien_Simplexml_Config
     */
    public function getResult()
    {
        $items = array();
        foreach ($this->_commands as $command) {
            $items = $command->execute($this->_factory->getModelInstance('Mage_Backend_Model_Menu_Item'));
        }

        foreach($items as $item) {
            if (is_null($item->getParentId())) {
                $this->_root->appendChild($item);
            } else {
                $items[$item->getParentId()] = $item;
            }
        }

        return new Varien_Simplexml_Config($this->_root);
    }
}
