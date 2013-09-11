<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sitemap grid link column renderer
 *
 * @category   Magento
 * @package    Magento_Sitemap
 */
namespace Magento\Adminhtml\Block\Sitemap\Grid\Renderer;

class Time extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * Prepare link to display in grid
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $time =  date('Y-m-d H:i:s', strtotime($row->getSitemapTime()) + \Mage::getSingleton('Magento\Core\Model\Date')->getGmtOffset());

        return $time;
    }

}
