<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\Data;

use \Magento\Core\Model\File\Uploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\Filesystem;
use \Magento\Core\Model\File\Validator\NotProtectedExtension;
use \Magento\Core\Helper\File\Storage;
use \Magento\Core\Helper\File\Storage\Database;
use \Magento\Downloadable\Model\Link as LinkConfig;
use \Magento\Downloadable\Model\Sample as SampleConfig;

class FileContentUploader extends Uploader implements FileContentUploaderInterface
{
    /**
     * Default MIME type
     */
    const DEFAULT_MIME_TYPE = 'application/octet-stream';

    /**
     * Filename prefix for temporary files
     *
     * @var string
     */
    protected $filePrefix = 'magento_api';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $systemTmpDirectory;

    /**
     * @var LinkConfig
     */
    protected $linkConfig;

    /**
     * @var SampleConfig
     */
    protected $sampleConfig;

    /**
     * @param Database $coreFileStorageDb
     * @param Storage $coreFileStorage
     * @param NotProtectedExtension $validator
     * @param Filesystem $filesystem
     * @param LinkConfig $linkConfig
     * @param SampleConfig $sampleConfig
     */
    public function __construct(
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        NotProtectedExtension $validator,
        Filesystem $filesystem,
        LinkConfig $linkConfig,
        SampleConfig $sampleConfig
    ) {
        $this->_validator = $validator;
        $this->_coreFileStorage = $coreFileStorage;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA_DIR);
        $this->systemTmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP_DIR);
        $this->linkConfig = $linkConfig;
        $this->sampleConfig = $sampleConfig;
    }

    /**
     * Decode base64 encoded content and save it in system tmp folder
     *
     * @param FileContent $fileContent
     * @return array
     */
    protected function decodeContent(FileContent $fileContent)
    {
        $tmpFileName = $this->getTmpFileName();
        $fileSize = $this->systemTmpDirectory->writeFile($tmpFileName, base64_decode($fileContent->getData()));

        return array(
            'name' => $fileContent->getName(),
            'type' => self::DEFAULT_MIME_TYPE,
            'tmp_name' => $this->systemTmpDirectory->getAbsolutePath($tmpFileName),
            'error' => 0,
            'size' => $fileSize,
        );
    }

    /**
     * Generate temporary file name
     *
     * @return string
     */
    protected function getTmpFileName()
    {
        return uniqid($this->filePrefix, true);
    }

    /**
     * {@inheritdoc}
     */
    public function upload(FileContent $fileContent, $contentType)
    {
        $this->_file = $this->decodeContent($fileContent);
        if (!file_exists($this->_file['tmp_name'])) {
            throw new \InvalidArgumentException('There was an error during file content upload.');
        }
        $this->_fileExists = true;
        $this->_uploadType = self::SINGLE_STYLE;
        $this->setAllowRenameFiles(true);
        $this->setFilesDispersion(true);
        $result = $this->save($this->getDestinationDirectory($contentType));
        $result['status'] = 'new';
        $result['name'] = substr($result['file'], strrpos($result['file'], '/') + 1);
        return $result;
    }

    /**
     * Retrieve destination directory for given content type
     *
     * @param string $contentType
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getDestinationDirectory($contentType)
    {
        switch ($contentType) {
            case 'link_file':
                $directory = $this->mediaDirectory->getAbsolutePath($this->linkConfig->getBaseTmpPath());
                break;
            case 'link_sample_file':
                $directory = $this->mediaDirectory->getAbsolutePath($this->linkConfig->getBaseSampleTmpPath());
                break;
            case 'sample':
                $directory = $this->mediaDirectory->getAbsolutePath($this->sampleConfig->getBaseTmpPath());
                break;
            default:
                throw new \InvalidArgumentException('Invalid downloadable file content type.');
        }
        return $directory;
    }
}
