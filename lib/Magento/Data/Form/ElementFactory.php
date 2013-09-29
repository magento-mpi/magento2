<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Data\Form;

class ElementFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create Magento data form with provided params
     *
     * @param string $elementClass
     * @param array $data
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function create($elementClass, array $data = array())
    {
        return $this->_objectManager->create($elementClass, array('attributes' => $data));
    }
}
