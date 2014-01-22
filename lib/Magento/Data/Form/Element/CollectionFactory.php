<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Form\Element;

use Magento\Data\Form\Element\Collection;
use Magento\ObjectManager;


class CollectionFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;


    /**
     * @param \Magento\ObjectManager $objectManager
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
        return $this->_objectManager->create('Magento\Data\Form\Element\Collection', $data);
    }
}
