<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class CreditmemoMapper
 */
class CreditmemoMapper
{
    /**
     * @var CreditmemoBuilder
     */
    protected $creditmemoBuilder;

    /**
     * @var CreditmemoItemMapper
     */
    protected $creditmemoItemMapper;

    /**
     * @param CreditmemoBuilder $creditmemoBuilder
     * @param CreditmemoItemMapper $creditmemoItemMapper
     */
    public function __construct(CreditmemoBuilder $creditmemoBuilder, CreditmemoItemMapper $creditmemoItemMapper)
    {
        $this->creditmemoBuilder = $creditmemoBuilder;
        $this->creditmemoItemMapper = $creditmemoItemMapper;
    }

    /**
     * Returns array of items
     *
     * @param Creditmemo $creditmemo
     * @return array
     */
    protected function getItems(Creditmemo $creditmemo)
    {
        $items = [];
        foreach ($creditmemo->getAllItems() as $item) {
            $items[] = $this->creditmemoItemMapper->extractDto($item);
        }

        return $items;
    }

    /**
     * @param Creditmemo $creditmemo
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function extractDto(Creditmemo $creditmemo)
    {
        $this->creditmemoBuilder->populateWithArray($creditmemo->getData());
        $this->creditmemoBuilder->setItems($this->getItems($creditmemo));

        return $this->creditmemoBuilder->create();
    }
}
