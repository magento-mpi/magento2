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
namespace Magento\Sitemap\Block\Adminhtml\Grid\Renderer;

class Time extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Stdlib\DateTime\DateTime $date
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Stdlib\DateTime\DateTime $date,
        array $data = array()
    ) {
        $this->_date = $date;
        parent::__construct($context, $data);
    }

    /**
     * Prepare link to display in grid
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $time =  date('Y-m-d H:i:s', strtotime($row->getSitemapTime()) + $this->_date->getGmtOffset());
        return $time;
    }

}
