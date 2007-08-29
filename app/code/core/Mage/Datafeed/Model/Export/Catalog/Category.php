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
 * Export catalog Categories
 *
 * @category   Mage
 * @package    Mage_Datafeed
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Datafeed_Model_Export_Catalog_Category extends Varien_Object 
{
    public function __construct() 
    {
        
    }
    
    public function getCategoriesList($parentId)
    {
        $nodes = Mage::getResourceModel('catalog/category_tree')
            ->joinAttribute('name')
            ->joinAttribute('description')
            ->load($parentId)
            ->getNodes();
         
        return $nodes;
    }
}
