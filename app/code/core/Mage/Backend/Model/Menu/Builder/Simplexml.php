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
     * @var Varien_Simplexml_Config
     */
    protected $_root;

    public function __construct(array $data = array())
    {
        if (!isset($data['factory'])) {
            throw new InvalidArgumentException();
        }
        $this->_factory = $data['factory'];

        if (!isset($data['tree']) || !($data['tree'] instanceof Varien_Simplexml_Config)) {
            throw new InvalidArgumentException();
        }
        $this->_root = $data['tree'];
    }

    /**
     * @return Varien_Simplexml_Config
     */
    public function getResult()
    {
        $root = new Varien_Simplexml_Element('<menu/>');
        /** @var $items Mage_Backend_Model_Menu_Item[] */
        $items = array();
        foreach ($this->_commands as $command) {
            $item = $command->execute($this->_factory->getModelInstance('Mage_Backend_Model_Menu_Item'));
            $items[$item->getAttribute('id')] = $item;
        }

        foreach($items as $item) {
            if (is_null($item->getAttribute('parent'))) {
                $items[$item->getAttribute('parent')] = $root->appendChild($item);
            } else {
                if (!isset($items[$item->getAttribute('parent')])) {
                    throw new OutOfBoundsException();
                }
                $items[$item->getAttribute('parent')]->appendChild($item);
            }
        }
        return new $this->_root->setNode($root);
    }
}
