<?php
/**
 * 
 *
 * @file        Abstract.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

abstract class Varien_Image_Adapter_Abstract
{

    public $fileName = null;

    public $imageBackgroundColor = 0;

    protected $_fileType = null;

    protected $_fileMimeType = null;

    protected $_fileSrcName = null;

    protected $_fileSrcPath = null;

    protected $_imageHandler = null;

    protected $_imageSrcWidth = null;

    protected $_imageSrcHeight = null;

    protected $_requiredExtensions = null;

    abstract public function open($fileName);

    abstract public function save($destination=null, $newName=null);

    abstract public function display();

    abstract public function resize($width=null, $height=null);

    abstract public function rotate($angle=null);

    abstract public function crop($top=0, $left=0, $right=0, $bottom=0);

    abstract public function watermark($watermarkImage=null, $positionX=0, $positionY=0, $watermarkImageOpacity=30, $repeat=false);

    abstract public function checkDependencies();

    public function getMimeType()
    {
        if( $this->_fileType ) {
            return $this->_fileType;
        } else {
            list($this->_imageSrcWidth, $this->_imageSrcHeight, $this->_fileType, ) = getimagesize($this->_fileName);
            $this->_fileMimeType = image_type_to_mime_type($this->_fileType);
            return $this->_fileMimeType;
        }
    }

    protected function _getFileAttributes()
    {
        $pathinfo = pathinfo($this->_fileName);

        $this->_fileSrcPath = $pathinfo['dirname'];
        $this->_fileSrcName = $pathinfo['basename'];
    }

} 
 
// ft:php
// fileformat:unix
// tabstop:4
