<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once 'Writer.php';
require_once 'Reader.php';
require_once 'LoggerAbstract.php';


class Tools_Migration_Acl_Db_Updater
{
    const WRITE_MODE = 'write';

    /**
     * Resource id reader
     *
     * @var Tools_Migration_Acl_Db_Reader
     */
    protected $_reader;

    /**
     * Resource id writer
     *
     * @var Tools_Migration_Acl_Db_Writer
     */
    protected $_writer;

    /**
     * Operation logger
     *
     * @var Tools_Migration_Acl_Db_LoggerAbstract
     */
    protected $_logger;

    /**
     * Migration mode
     *
     * @var string
     */
    protected $_mode;

    /**
     * @param Tools_Migration_Acl_Db_Reader $reader
     * @param Tools_Migration_Acl_Db_Writer $writer
     * @param Tools_Migration_Acl_Db_LoggerAbstract $logger
     * @param string $mode - if value is "preview" migration does not happen
     */
    public function __construct(
        Tools_Migration_Acl_Db_Reader $reader,
        Tools_Migration_Acl_Db_Writer $writer,
        Tools_Migration_Acl_Db_LoggerAbstract $logger,
        $mode
    ) {
        $this->_reader = $reader;
        $this->_writer = $writer;
        $this->_logger = $logger;
        $this->_mode = $mode;
    }

    /**
     * Migrate old keys to new
     *
     * @param array $map
     */
    public function migrate($map)
    {
        foreach ($this->_reader->fetchAll() as $oldKey => $count) {
            $newKey = isset($map[$oldKey]) ? $map[$oldKey] : null;
            if (in_array($oldKey, $map)) {
                $newKey = $oldKey;
                $oldKey = null;
            }
            if ($newKey && $oldKey && $this->_mode == self::WRITE_MODE) {
                $this->_writer->update($oldKey, $newKey);
            }
            $this->_logger->add($oldKey, $newKey, $count);
        }
    }
}
