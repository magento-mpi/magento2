<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category image attribute backend model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Category\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($logger);
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\Object $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName() . '_additional_data');

        // if no image was set - nothing to do
        if (empty($value) && empty($_FILES)) {
            return $this;
        }

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA_DIR
        )->getAbsolutePath(
            'catalog/category/'
        );

        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Core\Model\File\Uploader::TMP_NAME_EMPTY) {
                $this->_logger->logException($e);
            }
        }
        return $this;
    }
}
