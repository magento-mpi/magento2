<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template filter factory
 */
namespace Magento\Catalog\Model\Template\Filter;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create template filter
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\Filter\Template
     * @throws \Magento\Framework\Model\Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof \Magento\Framework\Filter\Template) {
            throw new \Magento\Framework\Model\Exception($className . ' doesn\'t extends \Magento\Framework\Filter\Template');
        }
        return $filter;
    }
}
