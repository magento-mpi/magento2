<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Form\Element;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create form element with provided params
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function create($className, array $data = array())
    {
        return $this->_objectManager->create($className, array('attributes' => $data));
    }
}
