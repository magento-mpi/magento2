<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product image attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

class Image
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Dir model
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Dir $dir
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Magento\Core\Model\Dir $dir,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_dir = $dir;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($logger);
    }

    /**
     * After save
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
        } catch (\Exception $e){
            return $this;
        }
        $uploader->save($this->_dir->getDir(\Magento\Core\Model\Dir::MEDIA) . '/catalog/product');

        $fileName = $uploader->getUploadedFileName();
        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()
                 ->saveAttribute($object, $this->getAttribute()->getName());
        }
        return $this;
    }
}
