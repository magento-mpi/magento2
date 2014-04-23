<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Product image attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Image extends AbstractBackend
{
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
     * After save
     *
     * @param \Magento\Framework\Object $object
     * @return $this|void
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
        } catch (\Exception $e) {
            return $this;
        }
        $path = $this->_filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem::MEDIA_DIR
        )->getAbsolutePath(
            'catalog/product/'
        );
        $uploader->save($path);

        $fileName = $uploader->getUploadedFileName();
        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        }
        return $this;
    }
}
