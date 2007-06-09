<?php
/**
 * Export catalog product
 *
 * @package     Mage
 * @subpackage  Datafeed
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * TODO         formatCSV() method.
 */
class Mage_Datafeed_Model_Export_Catalog_Product extends Varien_Object 
{

    private $csvDelimiter = ";";

    private $csvEOL = "\r\n";

    public function __construct() 
    {
        
    }
    
    public function getCategoryProducts($categoryId, $count=10)
    {
        $collection = Mage::getModel('catalog_resource/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addCategoryFilter($categoryId)
            ->setPageSize($count)
            ->setOrder('create_date', 'desc')
            ->load();
        return $collection;
    }

    public function getCsvDelimiter()
    {
        return $this->csvDelimiter;
    }

    public function getCsvEOL()
    {
        return $this->csvEOL;
    }

    public function setCsvDelimiter($delimiter)
    {
        $this->csvDelimiter = $delimiter;
    }

    public function setCsvEOL($eol)
    {
        $this->csvEOL = $eol;
    }

    public function formatCSV($item)
    {
        return $item;
    }

}
