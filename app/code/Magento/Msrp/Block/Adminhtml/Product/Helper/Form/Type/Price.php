<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Block\Adminhtml\Product\Helper\Form\Type;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

/**
 * Product form MSRP field helper
 */
class Price extends \Magento\Framework\Data\Form\Element\Select
{
    /** @var \Magento\Msrp\Helper\Data */
    protected $msrpData;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Msrp\Helper\Data $msrpData,
        $data = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->msrpData = $msrpData;
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function toHtml()
    {
        if ($this->msrpData->isMsrpEnabled()) {
            return parent::toHtml();
        }
        return '';
    }
}
