<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer order taxes xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Totals_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    /**
     * Add order taxes rendered to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $totalsXmlObj
     * @return null
     */
    public function addToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $totalsXmlObj)
    {
        /** @var $taxesXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $taxesXmlObj = $totalsXmlObj->addChild('tax');

        $fullInfo = $this->getOrder()->getFullTaxInfo();

        if ($this->displayFullSummary() && !empty($fullInfo)) {
            foreach ((array)$fullInfo as $info) {
                if (isset($info['hidden']) && $info['hidden']) {
                    continue;
                }

                foreach ((array)$info['rates'] as $rate) {
                    if (isset($info['amount'])) {
                        $config = array('label' => $rate['title']);
                        if (!is_null($rate['percent'])) {
                            $config['percent'] = sprintf('(%0.2f%%)', $rate['percent']);
                        }
                        $taxesXmlObj->addCustomChild(
                            'item', is_null($rate['percent']) ? '' : $this->_formatPrice($info['amount']), $config
                        );
                    }
                }
            }
        }

        $taxesXmlObj->addCustomChild(
            'summary', $this->_formatPrice($this->getSource()->getTaxAmount()), array('label' => $this->__('Tax'))
        );
    }

    /**
     * Format price using order currency
     *
     * @param   float $amount
     * @return  string
     */
    protected function _formatPrice($amount)
    {
        return Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->formatPrice($this, $amount);
    }
}
