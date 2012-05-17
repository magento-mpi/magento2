<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Image_Adapter_Imagemagic extends Varien_Image_Adapter_Abstract
{
    /**
     * Whether image was resized or not
     *
     * @var bool
     */
    protected $_resized = false;

    /**
     * Options Container
     *
     * @var array
     */
    protected $_options = array(
        'resolution' => array(
            'x' => 72,
            'y' => 72
        )
    );

    /**
     * Set/get background color. Check Imagick::COLOR_* constants
     *
     * @param int|string|array $color
     * @return int
     */
    public function backgroundColor($color = null)
    {
        if ($color) {
            if (is_array($color)) {
                $color = "rgb(" . join(',', $color) . ")";
            }

            $pixel = new ImagickPixel;
            if (is_numeric($color)) {
                $pixel->setColorValue($color, 1);
            } else {
                $pixel->setColor($color);
            }
            $this->_imageHandler->setImageBackgroundColor($color);
        } else {
            $pixel = $this->_imageHandler->getImageBackgroundColor();
        }

        $this->imageBackgroundColor = $pixel->getColorAsString();

        return $this->imageBackgroundColor;
    }

    /**
     * Open image for processing
     *
     * @throws RuntimeException if image format is unsupported
     * @param string $filename
     */
    public function open($filename)
    {
        $this->_fileName = $filename;
        $this->_getFileAttributes();

        try {
            $this->_imageHandler = new Imagick($this->_fileName);
        } catch (ImagickException $e) {
            throw new RuntimeException('Unsupported image format.', $e->getCode(), $e);
        }

        $this->backgroundColor();
        $this->getMimeType();
    }

    /**
     * Save image to specific path.
     * If some folders of path does not exist they will be created
     *
     * @throws Exception  if destination path is not writable
     * @param string $destination
     * @param string $newName
     */
    public function save($destination=null, $newName=null)
    {
        $fileName = ( !isset($destination) ) ? $this->_fileName : $destination;

        if (isset($destination) && isset($newName)) {
            $fileName = $destination . DIRECTORY_SEPARATOR . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $info = pathinfo($destination);
            $fileName = $destination;
            $destination = $info['dirname'];
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . DIRECTORY_SEPARATOR . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }

        $destinationDir = isset($destination) ? $destination : $this->_fileSrcPath;

        if(!is_writable($destinationDir)) {
            try {
                $io = new Varien_Io_File();
                $result = $io->mkdir($destination);
            } catch (Exception $e) {
            }
            if (isset($e) || !$result) {
                throw new Exception("Unable to write file into directory '{$destinationDir}'. Access forbidden.");
            }
        }

        $this->_applyOptions();
        $this->_imageHandler->stripImage();
        $this->_imageHandler->writeImage($fileName);
    }

    /**
     * Apply options to image. Will be usable later when create an option container
     *
     * @return Varien_Image_Adapter_Imagemagic
     */
    protected function _applyOptions()
    {
        $this->_imageHandler->setImageCompressionQuality($this->quality());
        $this->_imageHandler->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->_imageHandler->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $this->_imageHandler->setImageResolution($this->_options['resolution']['x'], $this->_options['resolution']['y']);
        if (method_exists($this->_imageHandler, 'optimizeImageLayers')) {
            $this->_imageHandler->optimizeImageLayers();
        }

        return $this;
    }

    /**
     * Put image into output stream
     *
     */
    public function display()
    {
        header("Content-type: " . $this->getMimeType());
        $this->_applyOptions();
        echo (string)$this->_imageHandler;
    }

    /**
     * Change the image size
     *
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function resize($frameWidth = null, $frameHeight = null)
    {
        $dims = $this->_adaptResizeValues($frameWidth, $frameHeight);

        if ($dims['dst']['width'] > $this->_imageHandler->getImageWidth() ||
            $dims['dst']['height'] > $this->_imageHandler->getImageHeight()
        ) {
            $this->_imageHandler->sampleImage($dims['dst']['width'], $dims['dst']['height']);
        } else {
            $this->_imageHandler->resizeImage($dims['dst']['width'], $dims['dst']['height'], Imagick::FILTER_LANCZOS, true);
        }

        if ($this->_imageHandler->getImageWidth() < 300
            || $this->_imageHandler->getImageHeight() < 300
        ) {
            $this->_imageHandler->sharpenImage(4, 1);
        }

        $this->refreshImageDimensions();
        $this->_resized = true;
    }

    /**
     * Rotate image on specific angle
     *
     * @param int $angle
     */
    public function rotate($angle)
    {
        # compatibility with GD2 adapter
        $angle = 360 - $angle;
        $pixel = new ImagickPixel;
        $pixel->setColor("rgb(" . $this->imageBackgroundColor . ")");

        $this->_imageHandler->rotateImage($pixel, $angle);
        $this->refreshImageDimensions();
    }

    /**
     * Crop image
     *
     * @param int $top
     * @param int $left
     * @param int $right
     * @param int $bottom
     */
    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0)
    {
        if( $left == 0 && $top == 0 && $right == 0 && $bottom == 0 ) {
            return;
        }

        $newWidth  = $this->_imageSrcWidth  - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top  - $bottom;

        $this->_imageHandler->cropImage($newWidth, $newHeight, $left, $top);
        $this->refreshImageDimensions();
    }

    /**
     * Add watermark to image
     *
     * @param string $imagePath
     * @param int $positionX
     * @param int $positionY
     * @param int $watermarkImageOpacity
     * @param bool $isWaterMarkTile
     */
    public function watermark($imagePath, $positionX = 0, $positionY = 0, $opacity = 30, $isWaterMarkTile = false)
    {
        $opacity = $this->getWatermarkImageOpacity()
            ? $this->getWatermarkImageOpacity()
            : $opacity;

        $opacity = (float)number_format($opacity / 100, 1);
        $watermark = new Imagick($imagePath);

        $iterator = $watermark->getPixelIterator();

        if (method_exists($watermark, 'setImageOpacity')) {
            // available from imagick 6.2.9
            $watermark->setImageOpacity($opacity);
        } else {
            // go to each pixel and make it transparent
            foreach ($iterator as $y => $pixels) {
                foreach ($pixels as $x => $pixel) {
                    $watermark->paintTransparentImage($pixel, $opacity, 65535);
                }

                $iterator->syncIterator();
            }
        }

        switch ($this->getWatermarkPosition()) {
            case self::POSITION_STRETCH:
                $watermark->sampleImage($this->_imageSrcWidth, $this->_imageSrcHeight);
                break;
            case self::POSITION_CENTER:
                $positionX = ($this->_imageSrcWidth  - $watermark->getImageWidth())/2;
                $positionY = ($this->_imageSrcHeight - $watermark->getImageHeight())/2;
                break;
            case self::POSITION_TOP_RIGHT:
                $positionX = $this->_imageSrcWidth - $watermark->getImageWidth();
                break;
            case self::POSITION_BOTTOM_RIGHT:
                $positionX = $this->_imageSrcWidth  - $watermark->getImageWidth();
                $positionY = $this->_imageSrcHeight - $watermark->getImageHeight();
                break;
            case self::POSITION_BOTTOM_LEFT:
                $positionY = $this->_imageSrcHeight - $watermark->getImageHeight();
                break;
            case self::POSITION_TILE:
                $isWaterMarkTile = true;
                break;
        }

        try {
            if ($isWaterMarkTile) {
                $offsetX = $positionX;
                $offsetY = $positionY;
                while($offsetY <= ($this->_imageSrcHeight + $watermark->getImageHeight())) {
                    while($offsetX <= ($this->_imageSrcWidth + $watermark->getImageWidth())) {
                        $this->_imageHandler->compositeImage($watermark->getHandler(), Imagick::COMPOSITE_OVER, $offsetX, $offsetY);
                        $offsetX += $watermark->getImageWidth();
                    }
                    $offsetX = $positionX;
                    $offsetY += $watermark->getImageHeight();
                }
            } else {
                $this->_imageHandler->compositeImage($watermark, Imagick::COMPOSITE_OVER, $positionX, $positionY);
            }
        } catch (ImagickException $e) {
            throw new RuntimeException('Unable to create watermark.', $e->getCode(), $e);
        }

        // merge layers
        $this->_imageHandler->flattenImages();
        $watermark->destroy();
        $this->refreshImageDimensions();
    }

    /**
     * Checks required dependecies
     *
     * @throws Exception if some of dependecies are missing
     */
    public function checkDependencies()
    {
        if (!class_exists('Imagick')) {
            throw new Exception("Required PHP extension 'Imagick' was not loaded.");
        }
    }

    /**
     * Reassign image dimensions
     */
    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth  = $this->_imageHandler->getImageWidth();
        $this->_imageSrcHeight = $this->_imageHandler->getImageHeight();
        $this->_imageHandler->setImagePage($this->_imageSrcWidth, $this->_imageSrcHeight, 0, 0);
    }

    /**
     * Standard destructor. Destroy stored information about image
     *
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * Destroy stored information about image
     *
     * @return Varien_Image_Adapter_Imagemagic
     */
    public function destroy()
    {
        if (null !== $this->_imageHandler && $this->_imageHandler instanceof Imagick) {
            $this->_imageHandler->clear();
            $this->_imageHandler->destroy();
            $this->_imageHandler = null;
        }
        return $this;
    }

    /**
     * Returns the array of the specified pixel
     *
     * @param int $x
     * @param int $y
     * @param bool $returnString
     * @return string|array
     */
    public function getColorAt($x, $y, $returnArray = false)
    {
        $pixel = $this->_imageHandler->getImagePixelColor($x, $y);

        return $returnArray ? $pixel->getColor() : explode(',', $pixel->getColorAsString());
    }
}
