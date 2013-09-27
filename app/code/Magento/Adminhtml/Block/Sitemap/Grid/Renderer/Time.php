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
class Magento_Adminhtml_Block_Sitemap_Grid_Renderer_Time extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * @param Magento_Core_Model_Date $date
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Date $date,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_date = $date;
        parent::__construct($context, $data);
    }

    /**
     * Prepare link to display in grid
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $time =  date('Y-m-d H:i:s', strtotime($row->getSitemapTime()) + $this->_date->getGmtOffset());
        return $time;
    }

}
