<?php
/**
 * Image handler library
 *
 * @package     Varien
 * @subpackage  Image
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Varien_Image
{
    protected $_adapter;

    protected $_fileName;
    
    function __construct($adapter=Varien_Image_Adapter::ADAPTER_GD2, $fileName=null)
    {
        $this->_getAdapter($adapter);
        $this->_fileName = $fileName;
        if( isset($fileName) ) {
            $this->open();
        }
    }

    /**
     * Use this mathod to open image and create handle for opened image
     */
    public function open()
    {
        $this->_getAdapter()->checkDependencies();

        if( !file_exists($this->_fileName) ) {
            throw new Exception("File '{$this->_fileName}' does not exists.");
        }

        $this->_getAdapter()->open($this->_fileName);
    }

    /**
     * Use this method to display handled image
     */    
    public function display()
    {
        $this->_getAdapter()->display();
    }

    public function save($destination=null, $newFileName=null)
    {
        $this->_getAdapter()->save($destination, $newFileName);
    }
    
    public function rotate($angle)
    {
        $this->_getAdapter()->rotate($angle);
    }
    
    public function crop($top=0, $left=0, $right=0, $bottom=0)
    {
        $this->_getAdapter()->crop($left, $top, $right, $bottom);
    }
    
    public function resize($width=null, $height=null)
    {
        $this->_getAdapter()->resize($width, $height);
    }

    public function watermark($watermarkImage, $positionX=0, $positionY=0, $watermarkImageOpacity=30, $repeat=false)
    {
        if( !file_exists($watermarkImage) ) {
            throw new Exception("Required file '{$watermarkImage}' does not exists.");
        }
        $this->_getAdapter()->watermark($watermarkImage, $positionX, $positionY, $watermarkImageOpacity, $repeat);
    }
    
    public function getMimeType()
    {
        return $this->_getAdapter()->getMimeType();
    }

    public function process()
    {
        
    }

    public function instruction()
    {
        
    }

    public function setImageBackgroundColor($color)
    {
        $this->_getAdapter()->imageBackgroundColor = intval($color);
    }

    public function getCopy()
    {
        return clone $this;
    }

    protected function _getAdapter($adapter=null)
    {
        if( !isset($this->_adapter) ) {
            $this->_adapter = Varien_Image_Adapter::factory( $adapter );
        }
        return $this->_adapter;
    }

}
