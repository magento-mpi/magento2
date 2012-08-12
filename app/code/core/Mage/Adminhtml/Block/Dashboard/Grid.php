<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

 class Mage_Adminhtml_Block_Dashboard_Grid extends Mage_Adminhtml_Block_Widget_Grid
 {

    /**
     * Setting default for every grid on dashboard
     *
     */

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/grid.phtml');
        $this->setDefaultLimit(5);
    }
 }

