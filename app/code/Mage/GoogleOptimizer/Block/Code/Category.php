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
 * Google Optimizer Category Block
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Code_Category extends Mage_GoogleOptimizer_Block_Code
{
    protected function _initGoogleOptimizerModel()
    {
        $this->_setGoogleOptimizerModel($this->getGoogleOptimizer());
        return parent::_initGoogleOptimizerModel();
    }

    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    public function getGoogleOptimizer()
    {
        return $this->getCategory()->getGoogleOptimizerScripts();
    }
}
