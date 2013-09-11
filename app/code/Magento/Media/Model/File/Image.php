<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Media
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library file image resource model
 *
 * @category   Magento
 * @package    Magento_Media
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Media\Model\File;

class Image extends \Magento\Core\Model\Resource\AbstractResource
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * Retrieve connection for read data
     */
    protected function _getReadAdapter()
    {
        return false;
    }

    /**
     * Retrieve connection for write data
     */
    protected function _getWriteAdapter()
    {
        return false;
    }

    public function load(\Magento\Media\Model\Image $object, $file, $field=null)
    {
        // Do some implementation
        return $this;
    }

    public function save(\Magento\Media\Model\Image $object)
    {
        // Do some implementation
        return $this;
    }

    public function delete(\Magento\Media\Model\Image $object)
    {
        return $this;
    }

    /**
     * Create image resource for operation from file
     *
     * @param \Magento\Media\Model\Image $object
     * @return \Magento\Media\Model\File\Image
     */
    public function getImage(\Magento\Media\Model\Image $object)
    {
        $resource = false;
        switch(strtolower($object->getExtension())) {
            case 'jpg':
            case 'jpeg':
                $resource = imagecreatefromjpeg($object->getFilePath());
                break;

            case 'gif':
                $resource = imagecreatefromgif($object->getFilePath());
                break;

            case 'png':
                $resource = imagecreatefrompng($object->getFilePath());
                break;
        }

        if(!$resource) {
            \Mage::throwException(__('The image does not exist or is invalid.'));
        }


        return $resource;
    }

    /**
     * Create tmp image resource for operations
     *
     * @param \Magento\Media\Model\Image $object
     * @return \Magento\Media\Model\File\Image
     */
    public function getTmpImage(\Magento\Media\Model\Image $object)
    {
        $resource = imagecreatetruecolor($object->getDestanationDimensions()->getWidth(), $object->getDestanationDimensions()->getHeight());
        return $resource;
    }

    /**
     * Resize image
     *
     * @param \Magento\Media\Model\Image $object
     * @return \Magento\Media\Model\File\Image
     */
    public function resize(\Magento\Media\Model\Image $object)
    {
        $tmpImage = $object->getTmpImage();
        $sourceImage = $object->getImage();

        imagecopyresampled(
            $tmpImage,
            $sourceImage,
            0, 0, 0, 0,
            $object->getDestanationDimensions()->getWidth(),
            $object->getDestanationDimensions()->getHeight(),
            $object->getDimensions()->getWidth(),
            $object->getDimensions()->getHeight()
        );

        return $this;
    }

    /**
     * Add watermark for image
     *
     * @param \Magento\Media\Model\Image $object
     * @return \Magento\Media\Model\File\Image
     */
    public function watermark(\Magento\Media\Model\Image $object)
    {
        return $this;
    }

    /**
     * Creates image
     *
     * @param \Magento\Media\Model\Image $object
     * @param string|null $extension
     * @return \Magento\Media\Model\File\Image
     */
    public function saveAs(\Magento\Media\Model\Image $object, $extension=null)
    {
        if(is_null($extension)) {
            $extension = $object->getExtension();
        }

        $result = false;
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($object->getTmpImage(), $object->getFilePath(true), 80);
                break;
            case 'gif':
                $result = imagegif($object->getTmpImage(), $object->getFilePath(true));
                break;
            case 'png':
                $result = imagepng($object->getTmpImage(), $object->getFilePath(true));
                break;
        }

        if(!$result) {
            \Mage::throwException(__('Something went wrong while creating the image.'));
        }

        return $this;
    }

    /**
     * Retrive image dimensions
     *
     * @param \Magento\Media\Model\Image $object
     * @return \Magento\Object
     */
    public function getDimensions(\Magento\Media\Model\Image $object)
    {
        $info = @getimagesize($object->getFilePath());
        if(!$info) {
            \Mage::throwException(__('The image does not exist or is invalid.'));
        }

        $info = array('width'=>$info[0], 'height'=>$info[1], 'type'=>$info[2]);
        return new \Magento\Object($info);
    }

    /**
     * Destroys resource object
     *
     * @param resource $resource
     */
    public function destroyResource(&$resource)
    {
        imagedestroy($resource);
        return $this;
    }

    /**
     * Destroys resource object
     *
     * @param resource $resource
     */
    public function hasSpecialImage(\Magento\Media\Model\Image $object)
    {
        if(file_exists($object->getFilePath(true))) {
            return true;
        }

        return false;
    }


}
