<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\Acl\Db\Logger;

class Factory
{
    /**
     * List of allowed logger types
     * @var array
     */
    protected $_allowedLoggerTypes = array();

    public function __construct()
    {
        $this->_allowedLoggerTypes = array(
            'console',
            'file',
        );
    }

    /**
     * @param string $loggerType
     * @param string $filePath
     * @return \Magento\Tools\Migration\Acl\Db\LoggerAbstract
     * @throws \InvalidArgumentException
     */
    public function getLogger($loggerType, $filePath = null)
    {
        $loggerType = empty($loggerType) ? 'console' : $loggerType;
        if (false == in_array($loggerType, $this->_allowedLoggerTypes)) {
            throw new \InvalidArgumentException('Invalid logger type: ' . $loggerType);
        }

        $loggerClassName = null;
        switch ($loggerType) {
            case 'file':
                $loggerClassName = 'Magento\Tools\Migration\Acl\Db\Logger\File';
                break;
            default:
                $loggerClassName = 'Magento\Tools\Migration\Acl\Db\Logger\Console';
                break;
        }

        return new $loggerClassName($filePath);
    }
}
