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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category image attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Attribute_Backend_Gallery extends Mage_Eav_Model_Entity_Attribute_Backend_Gallery
{

    public function __construct()
    {
        parent::__construct();
        $resource = Mage::getSingleton('core/resource');
        $this->setConnection($resource->getConnection('catalog_read'), $resource->getConnection('catalog_write'));
        /**
         * 0 - big image
         * 1 - thumbnail
         */
        $this->_imageTypes = array(0, 1);
    }

}
