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
    
    public function __construct($adapter=Varien_Image_Adapter::ADAPTER_GD2, $fileName=null)
    {
        $this->_getAdapter($adapter);
        $this->_fileName = $fileName;
    }
    
    protected function _getAdapter($adapter=null)
    {
        if( !isset($this->_adapter) ) {
            $this->_adapter = Varien_Image_Adapter::factory( $adapter );
        }
        return $this->_adapter;
    }
    
    public function open()
    {
        $this->_getAdapter()->checkDependencies();

        if( !file_exists($this->_fileName) ) {
            throw new Exception("File '{$this->_fileName}' does not exists.");
        }

        $this->_getAdapter()->open($this->_fileName);
    }
    
    public function display()
    {
        $this->_getAdapter()->display();
    }
    
    public function save($destination=null, $newFileName=null)
    {
        $this->_getAdapter()->save($destination, $newFileName);
    }
    
    public function rotate($angle=null)
    {
        if( !isset($angle) ) {
            throw new Exception('Rotation angle can not be NULL.');
        }
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

    public function watermark($watermarkImage=null, $positionX=0, $positionY=0, $watermarkImageOpacity=30, $repeat=false)
    {
        if( !isset($watermarkImage) ) {
            throw new Exception('Watermark image can not be NULL.');
        } elseif( !file_exists($watermarkImage) ) {
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
}
