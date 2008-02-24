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
 * Catalog image helper
 *
 * @author Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Catalog_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_model;

    public function init(Mage_Catalog_Model_Product $product, $attributeName)
    {
        $this->_setModel( Mage::getModel('catalog/product_image') );
        $this->_getModel()->setBaseFile( $product->getData($attributeName) );
        $this->_getModel()->setDestinationSubdir($attributeName);
        return $this;
    }

    public function resize($width=null, $heigth=null)
    {
        $this->_getModel()
            ->setSize("{$width}x{$heigth}")
            ->resize();
        return $this;
    }

    public function rotate($angle)
    {
        $this->_getModel()
            ->rotate($angle);
        return $this;
    }

    public function watermark($fileName, $position)
    {
        $this->_getModel()
            ->setWatermark($fileName, $position);
        return $this;
    }

    public function getUrl()
    {
        if( $this->_getModel()->isCached() ) {
            return $this->_getModel()->getUrl();
        } else {
            return $this->_getModel()->saveFile()->getUrl();
        }
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }
}