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
 * Adminhtml dashboard grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    protected $_template = 'dashboard/grid.phtml';

    /**
     * Setting default for every grid on dashboard
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDefaultLimit(5);
    }
}
