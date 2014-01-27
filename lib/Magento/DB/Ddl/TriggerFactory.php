<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\DB\Ddl;

class TriggerFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    const INSTANCE_NAME = 'Magento\DB\Ddl\Trigger';

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\DB\Ddl\Trigger
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::INSTANCE_NAME, $data);
    }
}
