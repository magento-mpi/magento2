<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Pdf\Items\Invoice;

/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
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
     * @param \Magento\Framework\App\Filesystem $filesystem
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
        \Magento\Framework\App\Filesystem $filesystem,
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
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = array();

        // draw Product name
        $lines[0] = array(array('text' => $this->string->split($item->getName(), 35, true, true), 'feed' => 35));

        // draw SKU
        $lines[0][] = array(
            'text' => $this->string->split($this->getSku($item), 17),
            'feed' => 290,
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array('text' => $item->getQty() * 1, 'feed' => 435, 'align' => 'right');

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 395;
        $feedSubtotal = $feedPrice + 170;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = array('text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right');
                // draw Subtotal label
                $lines[$i][] = array('text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right');
                $i++;
            }
            // draw Price
            $lines[$i][] = array(
                'text' => $priceData['price'],
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'right'
            );
            // draw Subtotal
            $lines[$i][] = array(
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right'
            );
            $i++;
        }

        // draw Tax
        $lines[0][] = array(
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 495,
            'font' => 'bold',
            'align' => 'right'
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = array('text' => $this->string->split($value, 30, true, true), 'feed' => 40);
                    }
                }
            }
        }

        $lineBlock = array('lines' => $lines, 'height' => 20);

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
