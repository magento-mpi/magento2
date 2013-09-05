<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with bzip2 archives
 *
 * @category    Magento
 * @package     \Magento\Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Archive;

class Bz extends \Magento\Archive\AbstractArchive implements \Magento\Archive\ArchiveInterface
{

    /**
    * Pack file by BZIP2 compressor.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function pack($source, $destination)
    {
        $fileReader = new \Magento\Archive\Helper\File($source);
        $fileReader->open('r');

        $archiveWriter = new \Magento\Archive\Helper\File\Bz($destination);
        $archiveWriter->open('w');

        while (!$fileReader->eof()) {
            $archiveWriter->write($fileReader->read());
        }

        $fileReader->close();
        $archiveWriter->close();

        return $destination;
    }

    /**
    * Unpack file by BZIP2 compressor.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function unpack($source, $destination)
    {
        if (is_dir($destination)) {
            $file = $this->getFilename($source);
            $destination = $destination . $file;
        }

        $archiveReader = new \Magento\Archive\Helper\File\Bz($source);
        $archiveReader->open('r');

        $fileWriter = new \Magento\Archive\Helper\File($destination);
        $fileWriter->open('w');

        while (!$archiveReader->eof()) {
            $fileWriter->write($archiveReader->read());
        }

        return $destination;
    }

}
