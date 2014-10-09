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
     * @var \Magento\Framework\Service\Code\Generator\SampleDataInterface
     */
    protected $dataModel;

    /**
     * @param \Magento\Framework\Service\Code\Generator\SampleDataInterface $dataModel
     */
    public function __construct(\Magento\Framework\Service\Code\Generator\SampleDataInterface $dataModel)
    {
        parent::__construct($dataModel);
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->dataModel->setItems($items);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->dataModel->setName($name);
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->dataModel->setCount($count);
    }
}
