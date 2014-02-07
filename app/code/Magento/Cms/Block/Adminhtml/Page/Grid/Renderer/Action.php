<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Block\Adminhtml\Page\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\UrlInterface $urlBuilder,
        array $data = array()
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Object $row)
    {
        $this->urlBuilder->setScope($row->getData('_first_store_id'));
        $href = $this->urlBuilder->getUrl(
            $row->getIdentifier(),
            array(
                '_current' => false,
                '_query' => '___store=' . $row->getStoreCode()
           )
        );
        return '<a href="' . $href . '" target="_blank">' . __('Preview') . '</a>';
    }
}
