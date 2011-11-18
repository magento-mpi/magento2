<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with bzip2 archives
 *
 * @category    Mage
 * @package     Mage_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Archive_Bz extends Mage_Archive_Abstract implements Mage_Archive_Interface
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
        $data = $this->_readFile($source);
        $bzData = bzcompress($data, 9);
        $this->_writeFile($destination, $bzData);
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
        $data = '';
        $bzPointer = bzopen($source, 'r' );
        if (empty($bzPointer)) {
            throw new Exception('Can\'t open BZ archive : ' . $source);
        }
        while (!feof($bzPointer)) {
            $data .= bzread($bzPointer, 131072);
        }
        bzclose($bzPointer);
        if (is_dir($destination)) {
            $file = $this->getFilename($source);
            $destination = $destination . $file;
        }
        echo $destination;
        $this->_writeFile($destination, $data);
        return $destination;
    }

}
