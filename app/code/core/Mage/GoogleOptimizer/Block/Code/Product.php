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
 * Google Optmizer Product Block
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Code_Product extends Mage_GoogleOptimizer_Block_Code
{
    protected function _initGoogleOptimizerModel()
    {
        $this->_setGoogleOptimizerModel($this->getGoogleOptimizer());
        return parent::_initGoogleOptimizerModel();
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getGoogleOptimizer()
    {
        return $this->getProduct()->getGoogleOptimizerScripts();
    }
}
