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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Design_Source_Design extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $design = Mage::getModel('core/design_package')->getThemeList();
            $options = array();
            foreach ($design as $package=>$themes){
                $packageOption = array('label'=>$package);
                $themeOptions = array();
                foreach ($themes as $theme) {
                	$themeOptions[] = array('label'=>$theme, 'value'=>$package . '/' . $theme);
                }

                $packageOption['value'] = $themeOptions;

                $options[] = $packageOption;
            }
            array_unshift($options, array('value'=>'', 'label'=>''));
            $this->_options = $options;
        }
        return $this->_options;
    }
}