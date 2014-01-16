<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column block that is displayed only if rollback allowed
 *
 * @category   Magento
 * @package    Magento_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Block\Adminhtml\Grid\Column;

class Rollback extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $_backupHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backup\Helper\Data $backupHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backup\Helper\Data $backupHelper,
        array $data = array()
    ) {
        $this->_backupHelper = $backupHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check permission for rollback
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_backupHelper->isRollbackAllowed();
    }
}
