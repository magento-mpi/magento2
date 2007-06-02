<?php

class Varien_Image_Adapter_Gd2 extends Varien_Image_Adapter_Abstract
{

    function __construct()
    {
    }

    public function open($filename)
    {
        $this->_fileName = $filename;
        $this->getMimeType();
        $this->_getFileAttributes();
        switch( $this->_fileType ) {
            case IMAGETYPE_GIF:
                $this->_imageHandler = imagecreatefromgif($this->_fileName);
                break;

            case IMAGETYPE_JPEG:
                $this->_imageHandler = imagecreatefromjpeg($this->_fileName);
                break;

            case IMAGETYPE_PNG:
                $this->_imageHandler = imagecreatefrompng($this->_fileName);
                break;

            case IMAGETYPE_XBM:
                $this->_imageHandler = imagecreatefromxbm($this->_fileName);
                break;

            case IMAGETYPE_WBMP:
                $this->_imageHandler = imagecreatefromxbm($this->_fileName);
                break;

            default:
                throw new Exception("Unsupported image format.");
                break;
        }
    }

    public function save($destination=null, $newName=null)
    {
        $fileName = ( !isset($destination) ) ? $this->_fileName : $destination;

        if( isset($destination) && isset($newName) ) {
            $fileName = $destination . "/" . $fileName;
        } elseif( isset($destination) && !isset($newName) ) {
            $fileName = $destination . "/" . $this->_fileSrcName;
        } elseif( !isset($destination) && isset($newName) ) {
            $fileName = $this->_fileSrcPath . "/" . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }

        switch( $this->_fileType ) {
            case IMAGETYPE_GIF:
                imagegif($this->_imageHandler, $fileName);
                break;

            case IMAGETYPE_JPEG:
                imagejpeg($this->_imageHandler, $fileName);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->_imageHandler, $fileName);
                break;

            case IMAGETYPE_XBM:
                imagexbm($this->_imageHandler, $fileName);
                break;

            case IMAGETYPE_WBMP:
                imagewbmp($this->_imageHandler, $fileName);
                break;

            default:
                throw new Exception("Unsupported image format.");
                break;
        }

    }

    public function display()
    {
        header("Content-type: ".$this->getMimeType());
        switch( $this->_fileType ) {
            case IMAGETYPE_GIF:
                imagegif($this->_imageHandler);
                break;

            case IMAGETYPE_JPEG:
                imagejpeg($this->_imageHandler);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->_imageHandler);
                break;

            case IMAGETYPE_XBM:
                imagexbm($this->_imageHandler);
                break;

            case IMAGETYPE_WBMP:
                imagewbmp($this->_imageHandler);
                break;

            default:
                throw new Exception("Unsupported image format.");
                break;
        }
    }

    public function resize($dstWidth=null, $dstHeight=null)
    {
        if( !isset($dstWidth) && !isset($dstHeight) ) {
            throw new Exception("Invalid image dimensions.");
        }

        if ($this->_imageSrcWidth / $this->_imageSrcHeight >= $dstWidth / $dstHeight) {
            $width = $dstWidth;
            $xOffset = 0;

            $height = round(($width / $this->_imageSrcWidth) * $this->_imageSrcHeight);
            $yOffset = round(($dstHeight - $height) / 2);
        } else {
            $height = $dstHeight;
            $yOffset = 0;

            $width = round(($height / $this->_imageSrcHeight) * $this->_imageSrcWidth);
            $xOffset = round(($dstWidth - $width) / 2);
        }

        $imageNewHandler = imagecreatetruecolor($dstWidth, $dstHeight);

        imagecopyresampled($imageNewHandler, $this->_imageHandler, $xOffset, $yOffset, 0, 0, $width, $height, $this->_imageSrcWidth, $this->_imageSrcHeight);
        $this->_imageHandler = $imageNewHandler;
    }

    function __destruct()
    {
        imagedestroy($this->_imageHandler);
    }
}
