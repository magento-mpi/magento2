<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource\Form\Attribute;

class CollectionFactory
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
     * Create Collection
     *
     * @return \Magento\Customer\Model\Resource\Form\Attribute\Collection
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Customer\Model\Resource\Form\Attribute\Collection');
    }
}