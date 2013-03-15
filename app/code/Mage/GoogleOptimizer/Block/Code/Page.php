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
 * Google Optimizer Page Block
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Code_Page extends Mage_GoogleOptimizer_Block_Code
{
    protected function _initGoogleOptimizerModel()
    {
        $cmsPage = Mage::getSingleton('Mage_Cms_Model_Page');
        $this->_setGoogleOptimizerModel($cmsPage->getGoogleOptimizerScripts());
        return parent::_initGoogleOptimizerModel();
    }
}
