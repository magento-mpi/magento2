<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service\V1;

use Magento\TestModule2\Service\V1\Entity\ItemBuilder;
use Magento\TestModule2\Service\V1\Entity\Item;

class NoWebApiXml implements \Magento\TestModule2\Service\V1\NoWebApiXmlInterface
{
    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    /**
     * @param ItemBuilder $itemBuilder
     */
    public function __construct(ItemBuilder $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }
    
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        return $this->itemBuilder->setId($id)->setName('testProduct1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = $this->itemBuilder->setId(1)->setName('testProduct1')->create();

        $result2 = $this->itemBuilder->setId(2)->setName('testProduct2')->create();

        return array($result1, $result2);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return $this->itemBuilder->setId(rand())->setName($name)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return $this->itemBuilder->setId($item->getId())->setName('Updated' . $item->getName())->create();
    }
}
