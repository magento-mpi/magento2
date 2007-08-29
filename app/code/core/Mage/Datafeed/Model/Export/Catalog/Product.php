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
 * @package    Mage_Datafeed
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export catalog product
 *
 * @category   Mage
 * @package    Mage_Datafeed
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
        $collection = Mage::getResourceModel('catalog/product_collection')
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
