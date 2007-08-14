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

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Currency objects cache
	 */
	protected static $_currencies = array();

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
        	$currency_code = $this->_getCurrencyCode($row);
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
    
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }
        return false;
    }

    public function renderProperty()
    {
        $out = parent::renderProperty();
        $out.= ' width="140px" ';
        return $out;
    }
}