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
    public function render(\Magento\Object $row)
    {
        $this->_urlBuilder->setScope($row->getData('_first_store_id'));
        $href = $this->_urlBuilder->getUrl(
            $row->getIdentifier(),
            array(
                '_current' => false,
                '_query' => '___store=' . $row->getStoreCode()
           )
        );
        return '<a href="' . $href . '" target="_blank">' . __('Preview') . '</a>';
    }
}
