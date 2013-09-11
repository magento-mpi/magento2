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
 * Backup types option array
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Model\Grid;

class Options implements \Magento\Core\Model\Option\ArrayInterface
{

    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backup\Helper\Data $backupHelper
     */
    public function __construct(\Magento\Backup\Helper\Data $backupHelper)
    {
        $this->_helper = $backupHelper;
    }

    /**
     * Return backup types array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_helper->getBackupTypes();
    }
}
