<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Model\Widget\Instance;

class OptionsFactory
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
     * Create new action object
     *
     * @param $type
     * @param array $data
     * @return \Magento\Core\Model\Option\ArrayInterface
     */
    public function create($type, array $data = array())
    {
        return $this->_objectManager->create($type, $data);
    }
}
