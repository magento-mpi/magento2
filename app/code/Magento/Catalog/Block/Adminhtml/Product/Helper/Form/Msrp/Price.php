<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Msrp;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

/**
 * Product form MSRP field helper
 */
class Price extends \Magento\Framework\Data\Form\Element\Select
{
    /** @var \Magento\Catalog\Helper\Data */
    protected $catalogData;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Catalog\Helper\Data $catalogData,
        $data = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->catalogData = $catalogData;
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function toHtml()
    {
        if ($this->catalogData->isMsrpEnabled()) {
            return parent::toHtml();
        }
        return '';
    }
}
