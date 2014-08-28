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
    /** @var \Magento\Msrp\Model\Config */
    protected $config;

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Magento\Msrp\Model\Config $config
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Msrp\Model\Config $config,
        $data = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->config = $config;
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function toHtml()
    {
        if ($this->config->isEnabled()) {
            return parent::toHtml();
        }
        return '';
    }
}
