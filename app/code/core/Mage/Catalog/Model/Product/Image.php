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
 * Catalog product link model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Catalog_Model_Product_Image extends Mage_Core_Model_Abstract
{
    protected $_width;
    protected $_height;
    protected $_baseFile;
    protected $_newFile;
    protected $_processor;
    protected $_destinationSubdir;
    protected $_angle;
    protected $_watermarkPosition;
    protected $_watermarkWidth;
    protected $_watermarkHeigth;

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWidth($width)
    {
        $this->_width = $width;
        return $this;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setHeight($height)
    {
        $this->_height = $height;
        return $this;
    }

    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setSize($size)
    {
        $size = explode('x', strtolower($size));
        if( sizeof($size) < 2 ) {
            $this->setWidth(null)
                ->setHeight(null);
            return $this;
        }

        $this->setWidth( ($size[0] > 0) ? $size[0] : null )
            ->setHeight( ($size[1] > 0) ? $size[1] : null );

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        if( !$file ) {
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();

            if( file_exists($baseDir . Mage::getStoreConfig( "catalog/placeholder/{$this->getDestinationSubdir()}/placeholder" )) ) {
                $file = $baseDir . Mage::getStoreConfig( "catalog/placeholder/{$this->getDestinationSubdir()}/placeholder" );
            } else {
                $baseDir = Mage::getDesign()->getSkinBaseDir();
                if( file_exists( $baseDir . "images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg" ) ) {
                    $file = $baseDir . "images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                }
            }
        } else {
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            $baseFile = $baseDir . $file;
            if( !file_exists($baseFile) ) {
                if( file_exists( $baseDir . "images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg" ) )
                {
                    $baseFile = $baseDir . "images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                }
            }
        }

        if( !file_exists($baseFile) ) {
            throw new Exception(Mage::helper('catalog')->__('Image file not found'));
        }

        $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
        $destDir = $baseDir . '/cache/' . Mage::app()->getStore()->getId() . '/' . $this->getDestinationSubdir();

        if( is_null($this->getWidth()) && is_null($this->getHeight()) ) {
            $this->_newFile = $destDir . $file;
        } else {
            $this->_newFile = $destDir . "/{$this->getWidth()}x{$this->getHeight()}" . $file;
        }

        $this->_baseFile = $baseFile;

        return $this;
    }

    public function getBaseFile()
    {
        return $this->_baseFile;
    }

    public function getNewFile()
    {
        return $this->_newFile;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setImageProcessor($processor)
    {
        $this->_processor = $processor;
        return $this;
    }

    /**
     * @return Varien_Image
     */
    public function getImageProcessor()
    {
        if( !$this->_processor ) {
            $this->_processor = new Varien_Image($this->getBaseFile());
        }
        return $this->_processor;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function resize()
    {
        if( is_null($this->getWidth()) && is_null($this->getHeight()) ) {
            return $this;
        }
        $this->getImageProcessor()->resize($this->getWidth(), $this->getHeight());
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function rotate($angle)
    {
        $angle = intval($angle);
        $this->getImageProcessor()->rotate($angle);
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWatermark($file, $position=null, $size=null, $width=null, $heigth=null)
    {
        $filename = false;

        if( !$file ) {
            return $this;
        }

        $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();

        if( file_exists($baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file) ) {
            $filename = $baseDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . $file;
        } elseif ( file_exists($baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file) ) {
            $filename = $baseDir . '/watermark/websites/' . Mage::app()->getWebsite()->getId() . $file;
        } elseif ( file_exists($baseDir . '/watermark/default/' . $file) ) {
            $filename = $baseDir . '/watermark/default/' . $file;
        } elseif ( file_exists($baseDir . '/watermark/' . $file) ) {
            $filename = $baseDir . '/watermark/' . $file;
        } else {
            $baseDir = Mage::getDesign()->getSkinBaseDir();
            if( file_exists($baseDir . $file) ) {
                $filename = $baseDir . $file;
            }
        }

        if( $filename ) {
            $this->getImageProcessor()
                ->setWatermarkPosition( ($position) ? $position : $this->getWatermarkPosition() )
                ->setWatermarkWidth( ($width) ? $width : $this->getWatermarkWidth() )
                ->setWatermarkHeigth( ($heigth) ? $heigth : $this->getWatermarkHeigth() )
                ->watermark($filename);
        }

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function saveFile()
    {
        $this->getImageProcessor()->save($this->getNewFile());
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $baseDir = Mage::getBaseDir('media');
        $path = str_replace("{$baseDir}/", "", $this->_newFile);
        return Mage::getBaseUrl('media') . $path;
    }

    public function push()
    {
        $this->getImageProcessor()->display();
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setDestinationSubdir($dir)
    {
        $this->_destinationSubdir = $dir;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationSubdir()
    {
        return $this->_destinationSubdir;
    }

    public function isCached()
    {
        return file_exists($this->_newFile);
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        return $this;
    }

    public function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWatermarkSize($size)
    {
        if( is_array($size) ) {
            $this->setWatermarkWidth($size['width'])
                ->setWatermarkHeigth($size['heigth']);
        }
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWatermarkWidth($width)
    {
        $this->_watermarkWidth = $width;
        return $this;
    }

    public function getWatermarkWidth()
    {
        return $this->_watermarkWidth;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setWatermarkHeigth($heigth)
    {
        $this->_watermarkHeigth = $heigth;
        return $this;
    }

    public function getWatermarkHeigth()
    {
        return $this->_watermarkHeigth;
    }

    public function clearCache()
    {
        $directory = Mage::getBaseDir('media') . '/catalog/product/cache/';
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);
    }
}