<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Sales\Order\Pdf\Items;

/**
 * Order Creditmemo Downloadable Pdf Items renderer
 */
class Creditmemo extends \Magento\Downloadable\Model\Sales\Order\Pdf\Items\AbstractItems
{
    /**
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Filter\FilterManager $filterManager
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory
     * @param \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\App\Filesystem $filesystem,
        \Magento\Filter\FilterManager $filterManager,
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory,
        \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory,
        \Magento\Stdlib\String $string,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->string = $string;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $coreStoreConfig,
            $purchasedFactory,
            $itemsFactory,
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
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw Product name
        $lines[0] = array(array(
            'text' => $this->string->split($item->getName(), 35, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => $this->string->split($this->getSku($item), 17),
            'feed'  => 255,
            'align' => 'right'
        );

        // draw Total (ex)
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 330,
            'font'  => 'bold',
            'align' => 'right',
        );

        // draw Discount
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt(-$item->getDiscountAmount()),
            'feed'  => 380,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 445,
            'font'  => 'bold',
            'align' => 'right',
        );

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Total (inc)
        $subtotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount()
            - $item->getDiscountAmount();
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($subtotal),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                // draw options value
                $printValue = isset($option['print_value'])
                    ? $option['print_value']
                    : $this->filterManager->stripTags($option['value']);
                $lines[][] = array(
                    'text' => $this->string->split($printValue, 30, true, true),
                    'feed' => 40
                );
            }
        }

        // downloadable Items
        $purchasedItems = $this->getLinks()->getPurchasedItems();

        // draw Links title
        $lines[][] = array(
            'text' => $this->string->split($this->getLinksTitle(), 70, true, true),
            'font' => 'italic',
            'feed' => 35
        );

        // draw Links
        foreach ($purchasedItems as $link) {
            $lines[][] = array(
                'text' => $this->string->split($link->getLinkTitle(), 50, true, true),
                'feed' => 40
            );
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
