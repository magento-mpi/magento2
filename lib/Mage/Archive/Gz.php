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
 * Class to work with gz archives
 *
 * @category    Mage
 * @package     Mage_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Archive_Gz extends Mage_Archive_Abstract implements Mage_Archive_Interface
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
        $data = $this->_readFile($source);
        $gzData = gzencode($data, 9);
        $this->_writeFile($destination, $gzData);
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
        $gzPointer = gzopen($source, 'r' );
        if (empty($gzPointer)) {
            throw new Mage_Exception('Can\'t open GZ archive : ' . $source);
        }
        $data = '';
        while (!gzeof($gzPointer)) {
            $data .= gzread($gzPointer, 131072);
        }
        gzclose($gzPointer);
        if (is_dir($destination)) {
            $file = $this->getFilename($source);
            $destination = $destination . $file;
        }
        $this->_writeFile($destination, $data);
        return $destination;
    }

}