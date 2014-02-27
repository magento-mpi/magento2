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
     * @var \Magento\Url
     */
    protected $frontendUrlBuilder;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Url $frontendUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Url $frontendUrlBuilder,
        array $data = array()
    ) {
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Render action
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $this->frontendUrlBuilder->setScope($row->getData('_first_store_id'));
        $href = $this->frontendUrlBuilder->getUrl(
            $row->getIdentifier(),
            array(
                '_current' => false,
                '_query' => '___store=' . $row->getStoreCode()
           )
        );
        return '<a href="' . $href . '" target="_blank">' . __('Preview') . '</a>';
    }
}
