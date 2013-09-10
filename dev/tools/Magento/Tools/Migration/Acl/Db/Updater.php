<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_Acl_Db_Updater
{
    const WRITE_MODE = 'write';

    /**
     * Resource id reader
     *
     * @var Magento_Tools_Migration_Acl_Db_Reader
     */
    protected $_reader;

    /**
     * Resource id writer
     *
     * @var Magento_Tools_Migration_Acl_Db_Writer
     */
    protected $_writer;

    /**
     * Operation logger
     *
     * @var Magento_Tools_Migration_Acl_Db_LoggerAbstract
     */
    protected $_logger;

    /**
     * Migration mode
     *
     * @var string
     */
    protected $_mode;

    /**
     * @param Magento_Tools_Migration_Acl_Db_Reader $reader
     * @param Magento_Tools_Migration_Acl_Db_Writer $writer
     * @param Magento_Tools_Migration_Acl_Db_LoggerAbstract $logger
     * @param string $mode - if value is "preview" migration does not happen
     */
    public function __construct(
        Magento_Tools_Migration_Acl_Db_Reader $reader,
        Magento_Tools_Migration_Acl_Db_Writer $writer,
        Magento_Tools_Migration_Acl_Db_LoggerAbstract $logger,
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
