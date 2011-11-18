<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order invoice configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Config extends Mage_Core_Model_Config_Base
{
    protected $_totalModels = null;

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order_invoice'));
    }

    /**
     * Retrieve invoice total calculation models
     *
     * @return array
     */
    public function getTotalModels()
    {
        if (is_null($this->_totalModels)) {
            $this->_totalModels = array();
            $totalsConfig = $this->getNode('totals');
            foreach ($totalsConfig->children() as $totalCode=>$totalConfig) {
                $class = $totalConfig->getClassName();
                if ($class && ($model = Mage::getModel($class))) {
                    $this->_totalModels[] = $model;
                }
            }
        }
        return $this->_totalModels;
    }
}
