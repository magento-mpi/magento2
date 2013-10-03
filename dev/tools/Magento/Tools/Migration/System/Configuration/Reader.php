<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * System configuration migration reader
 */
namespace Magento\Tools\Migration\System\Configuration;

class Reader
{
    /**
     * @var \Magento\Tools\Migration\System\FileManager
     */
    protected $_fileManager;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Parser
     */
    protected $_parser;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Mapper
     */
    protected $_mapper;

    /**
     * @var string base application path
     */
    protected $_basePath;

    /**
     * pattern to find all system.xml files
     */
    CONST SYSTEM_CONFIG_PATH_PATTERN = 'app/code/*/*/*/etc/system.xml';

    /**
     * @param \Magento\Tools\Migration\System\FileManager $fileManager
     * @param \Magento\Tools\Migration\System\Configuration\Parser $parser
     * @param Tools_Migration_System_Configuration_Mapper $mapper
     */
    public function __construct(
        \Magento\Tools\Migration\System\FileManager $fileManager,
        \Magento\Tools\Migration\System\Configuration\Parser $parser,
        \Magento\Tools\Migration\System\Configuration\Mapper $mapper
    ) {
        $this->_fileManager = $fileManager;
        $this->_parser = $parser;
        $this->_mapper = $mapper;

        $this->_basePath = realpath(__DIR__ . '/../../../../../../..');
    }

    /**
     * Get configuration per file
     *
     * @return array
     */
    public function getConfiguration()
    {
        $files = $this->_fileManager->getFileList(
            $this->_basePath . DIRECTORY_SEPARATOR
            . \Magento\Tools\Migration\System\Configuration\Reader::SYSTEM_CONFIG_PATH_PATTERN
        );
        $result = array();
        foreach ($files as $fileName) {
            $result[$fileName] = $this->_mapper->transform(
                $this->_parser->parse(
                    $this->_getDOMDocument(
                        $this->_fileManager->getContents($fileName)
                    )
                )
            );
        }

        return $result;
    }

    /**
     * Create Dom document from xml string
     *
     * @param $xml
     * @return \DOMDocument
     */
    protected function _getDOMDocument($xml)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom;
    }
}
