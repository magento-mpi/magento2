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
 * Grid column block that is displayed only if rollback allowed
 *
 * @category   Magento
 * @package    \Magento\Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Block\Adminhtml\Grid\Column;

class Rollback extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * Check permission for rollback
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->helper('Magento\Backup\Helper\Data')->isRollbackAllowed();
    }
}
