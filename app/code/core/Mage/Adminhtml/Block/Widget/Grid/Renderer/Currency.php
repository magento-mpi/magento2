<?php
/**
 * Adminhtml grid item renderer currency
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka  <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Renderer_Currency extends Mage_Core_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface
{
	/**
	 * Currency objects cache
	 */
	protected static $_currencies = array();

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @param Varien_Object $column
     */
    public function render(Varien_Object $row, Varien_Object $column)
    {
        if ($data = $row->getData($column->getIndex())) {
        	$currency_code = $row->getData('currency_code');
        	if (!$currency_code) return $data;
        	if (!isset(self::$_currencies[$currency_code])) {
        		self::$_currencies[$currency_code] = Mage::getSingleton('directory/currency')->load($currency_code);
        	}
	       	if (self::$_currencies[$currency_code]->getCode()) {
				return self::$_currencies[$currency_code]->format($data);
        	}
        	return $data;
        }
        return null;
    }
}