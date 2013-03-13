<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Optimizer Block
 * to display conversion type scripts on pages setted in layout
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Code_Conversion extends Mage_GoogleOptimizer_Block_Code
{
    protected $_pageType = null;

    protected function _initGoogleOptimizerModel()
    {
        $collection = Mage::getModel('Mage_GoogleOptimizer_Model_Code')
            ->getCollection();

        if ($this->getPageType()) {
            $collection->addFieldToFilter('conversion_page', $this->getPageType());
        }

        $conversionCodes = array();
        foreach ($collection as $_item) {
            $conversionCodes[] = $_item->getConversionScript();
        }
        $this->_setGoogleOptimizerModel(
            new Varien_Object(array(
                'conversion_script' => implode('', $conversionCodes)
            ))
        );
        return parent::_initGoogleOptimizerModel();
    }

    public function setPageType($pageType)
    {
        $this->_pageType = $pageType;
        return $this;
    }

    public function getPageType()
    {
        return $this->_pageType;
    }
}
