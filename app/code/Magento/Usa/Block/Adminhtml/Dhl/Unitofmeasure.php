<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend model for DHL shipping methods for documentation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Block\Adminhtml\Dhl;

class Unitofmeasure extends \Magento\Backend\Block\System\Config\Form\Field
{

    /**
     * Usa data
     *
     * @var Magento_Usa_Helper_Data
     */
    protected $_usaData = null;

    /**
     * @param Magento_Usa_Helper_Data $usaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Usa_Helper_Data $usaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_usaData = $usaData;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Define params and variables
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $carrierModel = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl\International');

        $this->setInch($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'I')));
        $this->setCm($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'C')));

        $this->setHeight($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'height')));
        $this->setDepth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'depth')));
        $this->setWidth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'width')));

        $kgWeight = 70;

        $this->setDivideOrderWeightNoteKg(
            $this->jsQuoteEscape(__('This allows breaking total order weight into smaller pieces if it exceeds %1 %2 to ensure accurate calculation of shipping charges.', $kgWeight, 'kg'))
        );

        $weight = round(
            $this->_usaData->convertMeasureWeight(
                $kgWeight, \Zend_Measure_Weight::KILOGRAM, \Zend_Measure_Weight::POUND), 3);

        $this->setDivideOrderWeightNoteLbp(
            $this->jsQuoteEscape(__('This allows breaking total order weight into smaller pieces if it exceeds %1 %2 to ensure accurate calculation of shipping charges.', $weight, 'pounds'))
        );

        $this->setTemplate('dhl/unitofmeasure.phtml');
    }

    /**
     * Retrieve Element HTML fragment
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        return parent::_getElementHtml($element) . $this->_toHtml();
    }
}
