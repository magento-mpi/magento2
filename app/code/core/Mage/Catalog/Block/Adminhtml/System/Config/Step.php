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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin frontend model for Default Price Navigation Step config
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Adminhtml_System_Config_Step extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $scriptHtml = '<script type="text/javascript">;
            Validation.add(\'validate-number-range\', \''
                      . $this->__('The value is not within the specified range.') . '\', function(v, elm) {
                var result = Validation.get(\'IsEmpty\').test(v)
                    || (!isNaN(parseNumber(v)) && !/^\\s+$/.test(parseNumber(v)));
                var reRange = new RegExp(/^number\\-range\\-[^-]+-[^-]+$/);
                $w(elm.className).each(function(name, index) {
                    if (name.match(reRange) && result) {
                        var min = parseNumber(name.split("-")[2]);
                        var max = parseNumber(name.split("-")[3]);
                        if (!isNaN(min) && !isNaN(max)) {
                            var val = parseNumber(v);
                            result = (v >= min) && (v <= max);
                        }
                    }
                });
                return result;
            });
        </script>';
        return parent::render($element) . $scriptHtml;
    }
}
