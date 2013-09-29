<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup grid item renderer
 *
 * @category   Magento
 * @package    \Magento\Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Block\Adminhtml\Grid\Column\Renderer;

class Download
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return mixed
     */
    public function _getValue(\Magento\Object $row)
    {
        $url7zip = __('The archive can be uncompressed with <a href="%1">%2</a> on Windows systems.', 'http://www.7-zip.org/',
            '7-Zip');

        return '<a href="' . $this->getUrl('*/*/download',
            array('time' => $row->getData('time'), 'type' => $row->getData('type'))) . '">' . $row->getData('extension')
               . '</a> &nbsp; <small>(' . $url7zip . ')</small>';

    }
}
