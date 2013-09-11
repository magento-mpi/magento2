<?php
/**
 * ACL Role factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Rule;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of \Magento\Webapi\Model\Acl\Rule
     *
     * @param array $arguments fed into constructor
     * @return \Magento\Webapi\Model\Acl\Rule
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Webapi\Model\Acl\Rule', $arguments);
    }
}
