<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Dashboard;

/**
 * Adminhtml dashboard grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var string
     */
    protected $_template = 'dashboard/grid.phtml';

    /**
     * Setting default for every grid on dashboard
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDefaultLimit(5);
    }
}
