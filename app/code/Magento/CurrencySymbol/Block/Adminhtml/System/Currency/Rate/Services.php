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
 * Manage currency import services block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Block\Adminhtml\System\Currency\Rate;

class Services extends \Magento\Backend\Block\Template
{
    protected $_template = 'system/currency/rate/services.phtml';

    /**
     * Create import services form select element
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
                ->setOptions(\Mage::getModel('Magento\Backend\Model\Config\Source\Currency\Service')->toOptionArray(0))
                ->setId('rate_services')
                ->setName('rate_services')
                ->setValue(\Mage::getSingleton('Magento\Adminhtml\Model\Session')->getCurrencyRateService(true))
                ->setTitle(__('Import Service'))
        );

        return parent::_prepareLayout();
    }

}
