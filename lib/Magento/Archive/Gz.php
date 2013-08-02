<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with gz archives
 *
 * @category    Magento
 * @package     Magento_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Archive_Gz extends Magento_Archive_Abstract implements Magento_Archive_Interface
{
    /**
    * Pack file by GZ compressor.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function pack($source, $destination)
    {
        $fileReader = new Magento_Archive_Helper_File($source);
        $fileReader->open('r');

        $archiveWriter = new Magento_Archive_Helper_File_Gz($destination);
        $archiveWriter->open('wb9');

        while (!$fileReader->eof()) {
            $archiveWriter->write($fileReader->read());
        }

        $fileReader->close();
        $archiveWriter->close();

        return $destination;
    }

    /**
    * Unpack file by GZ compressor.
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

        $archiveReader = new Magento_Archive_Helper_File_Gz($source);
        $archiveReader->open('r');

        $fileWriter = new Magento_Archive_Helper_File($destination);
        $fileWriter->open('w');

        while (!$archiveReader->eof()) {
            $fileWriter->write($archiveReader->read());
        }

        return $destination;
    }
}