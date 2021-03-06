<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Document Type factory
 */
namespace Magento\Doc\Document\Type;

use Magento\Framework\Exception;

/**
 * Class Factory
 * @package Magento\Doc\Document\Type
 */
class Factory
{
    /**
     * Default document item type
     */
    const DEFAULT_TYPE = 'Magento\Doc\Document\Type\Article';

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $types;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $types
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, array $types = [])
    {
        $this->objectManager = $objectManager;
        $this->types = $types;
    }

    /**
     * Create model
     *
     * @param string $type
     * @return \Magento\Doc\Document\Type\AbstractType
     * @throws \Magento\Framework\Exception
     */
    public function get($type)
    {
        $className = isset($this->types[$type]) ? $this->types[$type] : self::DEFAULT_TYPE;
        $type = $this->objectManager->get($className);
        if (!$type instanceof AbstractType) {
            throw new Exception($className . ' doesn\'t extends \Magento\Doc\Document\Type\AbstractType');
        }
        return $type;
    }
}
