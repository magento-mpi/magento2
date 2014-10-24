<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Pdf\Items\Shipment;

/**
 * Sales Order Shipment Pdf default items renderer
 */
class DefaultShipment extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->string = $string;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = array();

        // draw Product name
        $lines[0] = array(array('text' => $this->string->split($item->getName(), 60, true, true), 'feed' => 100));

        // draw QTY
        $lines[0][] = array('text' => $item->getQty() * 1, 'feed' => 35);

        // draw SKU
        $lines[0][] = array(
            'text' => $this->string->split($this->getSku($item), 25),
            'feed' => 565,
            'align' => 'right'
        );

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110
                );

                // draw options value
                if ($option['value']) {
                    $printValue = isset(
                        $option['print_value']
                    ) ? $option['print_value'] : $this->filterManager->stripTags(
                        $option['value']
                    );
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = array('text' => $this->string->split($value, 50, true, true), 'feed' => 115);
                    }
                }
            }
        }

        $lineBlock = array('lines' => $lines, 'height' => 20);

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
