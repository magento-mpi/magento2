<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Directory
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Factory Interface for Directory
 * *
 * @category   Varien
 * @package    Varien_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */

interface IFactory{

    public function getFilesName(&$files);
    public function getFilesPaths(&$paths);
    public function getFilesObj(&$objs);
    public function useFilter($useFilter);
    public function getDirsName(&$dirs);
    public function toArray(&$arr);
    public function toXml(&$xml,$recursionLevel=0,$addOpenTag=true,$rootName='Struct');
}
