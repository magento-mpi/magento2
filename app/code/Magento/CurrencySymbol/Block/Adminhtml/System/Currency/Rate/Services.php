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
class Magento_CurrencySymbol_Block_Adminhtml_System_Currency_Rate_Services extends Magento_Backend_Block_Template
{
    /**
     * @inherit
     */
    protected $_template = 'system/currency/rate/services.phtml';

    /**
     * @var Magento_Backend_Model_Config_Source_Currency_ServiceFactory
     */
    protected $_srcCurrencyFactory;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_adminSession;

    /**
     * @param Magento_Backend_Model_Session $adminSession
     * @param Magento_Backend_Model_Config_Source_Currency_ServiceFactory $srcCurrencyFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Session $adminSession,
        Magento_Backend_Model_Config_Source_Currency_ServiceFactory $srcCurrencyFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminSession = $adminSession;
        $this->_srcCurrencyFactory = $srcCurrencyFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Create import services form select element
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
                ->setOptions($this->_srcCurrencyFactory->create()->toOptionArray(0))
                ->setId('rate_services')
                ->setName('rate_services')
                ->setValue($this->_adminSession->getCurrencyRateService(true))
                ->setTitle(__('Import Service'))
        );

        return parent::_prepareLayout();
    }
}
