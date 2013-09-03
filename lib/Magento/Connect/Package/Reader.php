<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to get package.xml from different places.
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Package;

class Reader
{

    /**
    * Name of package file
    */
    const DEFAULT_NAME_PACKAGE = 'package.xml';

    /**
    * Temporary dir for extract DEFAULT_NAME_PACKAGE.
    */
    const PATH_TO_TEMPORARY_DIRECTORY = 'var/package/tmp/';

    /**
    * Current path to file.
    *
    * @var string
    */
    protected $_file = '';

    /**
    * Archivator is used for extract DEFAULT_NAME_PACKAGE.
    *
    * @var \Magento\Archive
    */
    protected $_archivator = null;

    /**
    * Constructor initializes $_file.
    *
    * @param string $file
    * @return \Magento\Connect\Package\Reader
    */
    public function __construct($file='')
    {
        if ($file) {
            $this->_file = $file;
        } else {
            $this->_file = self::DEFAULT_NAME_PACKAGE;
        }
        return $this;
    }

    /**
    * Retrieve archivator.
    *
    * @return \Magento\Archive
    */
    protected function _getArchivator()
    {
        if (is_null($this->_archivator)) {
            $this->_archivator = new \Magento\Archive();
        }
        return $this->_archivator;
    }

    /**
    * Open file directly or from archive and return his content.
    *
    * @return string Content of file $file
    */
    public function load()
    {
        if (!is_file($this->_file) || !is_readable($this->_file)) {
            throw new \Exception('Invalid package file specified.');
        }
        if ($this->_getArchivator()->isArchive($this->_file)) {
            @mkdir(self::PATH_TO_TEMPORARY_DIRECTORY, 0777, true);
            $this->_file = $this->_getArchivator()->extract(
                self::DEFAULT_NAME_PACKAGE,
                $this->_file,
                self::PATH_TO_TEMPORARY_DIRECTORY
            );
        }
        $xmlContent = $this->_readFile();
        return $xmlContent;
    }

    /**
    * Read content file.
    *
    * @return string Content of file $file
    */
    protected function _readFile()
    {
        $handle = fopen($this->_file, 'r');
        try {
            $data = $this->_loadResource($handle);
        } catch (\Magento\MagentoException $e) {
            fclose($handle);
            throw $e;
        }
        fclose($handle);
        return $data;
    }

    /**
    * Loads a package from specified resource
    *
    * @param resource $resource only file resources are supported at the moment
    * @return \Magento\Connect\Package
    */
    protected function _loadResource(&$resource)
    {
        $data = '';
        //var_dump("====", $res, get_resource_type($resource));
        if ('stream' === get_resource_type($resource)) {
            while (!feof($resource)) {
                $data .= fread($resource, 10240);
            }
        } else {
            throw new \Magento\MagentoException('Unsupported resource type');
        }
        return $data;
    }

}
