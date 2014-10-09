<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

/**
 * DataBuilder class for \Magento\Framework\Service\Code\Generator\SampleData
 */
class SampleDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param \Magento\Framework\App\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\App\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Framework\Service\Code\Generator\SampleDataInterface');
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->data['items'] = $items;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->data['count'] = $count;
    }

    /**
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->data['create_at'] = $createdAt;
    }
}
