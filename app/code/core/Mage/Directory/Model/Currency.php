<?php
/**
 * Currency model
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Currency extends Varien_Object
{
    protected $_filter;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    /**
     * Get currency resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('directory/currency');
    }
    
    /**
     * Get currency code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('currency_code');
    }
    
    /**
     * Load currncy 
     *
     * @param   string $code
     * @return  Mage_Directory_Model_Currency
     */
    public function load($code)
    {
        $this->setData($this->getResource()->load($code));
        return $this;
    }
    
    /**
     * Save currency
     *
     * @return Mage_Directory_Model_Currency
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Delete currncy
     *
     * @return Mage_Directory_Model_Currency
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Get currency rate
     *
     * @param   string $toCurrency
     * @return  double
     */
    public function getRate($toCurrency)
    {
        return $this->getResource()->getRate($this->getCode(), $toCurrency);
    }
    
    /**
     * Convert price to currency format
     *
     * @param   double $price
     * @param   string $toCurrency
     * @return  double
     */
    public function convert($price, $toCurrency=null)
    {
        if (is_null($toCurrency)) {
            return $price;
        }
        elseif ($rate = $this->getRate($toCurrency)) {
            return $price*$rate;
        }
        throw new Exception('Undefined rate from "'.$this->getCode().'-'.$toCurrency->getCode().'"');
    }
    
    /**
     * Get currency filter
     *
     * @return Mage_Directory_Model_Currency_Filter
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            $this->_filter = new Mage_Directory_Model_Currency_Filter(
                $this->getFormat(), 
                $this->getFormatDecimals(), 
                $this->getFormatDecPoint(), 
                $this->getFormatThousandsSep()
            );
        }
        
        return $this->_filter;        
    }
    
    /**
     * Format price to currency format
     *
     * @param   double $price
     * @return  string
     */
    public function format($price)
    {
        return $this->getFilter()->filter($price);
    }
}