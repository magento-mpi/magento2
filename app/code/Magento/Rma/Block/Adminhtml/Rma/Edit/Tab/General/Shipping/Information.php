<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

/**
 * Shipment Information block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Information
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging
{
    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('edit/shipping/information.phtml');
    }
}
