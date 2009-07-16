<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tool block to add new tab for cms page edit tab control
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Googleoptimizer_Block_Adminhtml_Cms_Page_Edit_Enable extends Mage_Adminhtml_Block_Template
{
    /**
     * Utility method to add google optimizer tab to cms page edit page
     * in case it enabled in system. Uses as parameters tab container name,
     * new tab name and tab block type.
     *
     * @param $container
     * @param $name
     * @param $block
     * @return Mage_Googleoptimizer_Block_Adminhtml_Cms_Page_Edit_Enable
     */
    public function ifGoogleOptiomizerEnabled($container, $name, $block)
    {
        if (Mage::helper('googleoptimizer')->isOptimizerActiveForCms()) {
            $containerBlock = $this->getLayout()->getBlock($container);
            if ($containerBlock) {
                $containerBlock->addTab($name, $block);
            }
        }

        return $this;
    }
}
