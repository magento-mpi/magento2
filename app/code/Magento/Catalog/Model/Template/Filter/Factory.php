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
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create template filter
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Filter\Template
     * @throws \Magento\Core\Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof \Magento\Filter\Template) {
            throw new \Magento\Core\Exception($className . ' doesn\'t extends \Magento\Filter\Template');
        }
        return $filter;
    }
}
