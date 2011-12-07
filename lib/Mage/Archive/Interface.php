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
 * Interface for work with archives
 *
 * @category    Mage
 * @package     Mage_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Archive_Interface
{
    /**
    * Pack file or directory.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function pack($source, $destination);

    /**
    * Unpack file or directory.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function unpack($source, $destination);
}