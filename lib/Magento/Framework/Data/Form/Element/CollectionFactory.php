<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Form\Element;

use Magento\ObjectManager;

class CollectionFactory
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create collection factory with specified parameters
     *
     * @param array $data
     * @return Collection
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Framework\Data\Form\Element\Collection', $data);
    }
}
