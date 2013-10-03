<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Message model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

class Message
{
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const SUCCESS   = 'success';
    
    protected function _factory($code, $type, $class='', $method='')
    {
        switch (strtolower($type)) {
            case self::ERROR :
                $message = new \Magento\Core\Model\Message\Error($code);
                break;
            case self::WARNING :
                $message = new \Magento\Core\Model\Message\Warning($code);
                break;
            case self::SUCCESS :
                $message = new \Magento\Core\Model\Message\Success($code);
                break;
            default:
                $message = new \Magento\Core\Model\Message\Notice($code);
                break;
        }
        $message->setClass($class);
        $message->setMethod($method);
        
        return $message;
    }
    
    public function error($code, $class='', $method='')
    {
        return $this->_factory($code, self::ERROR, $class, $method);
    }

    public function warning($code, $class='', $method='')
    {
        return $this->_factory($code, self::WARNING, $class, $method);
    }

    public function notice($code, $class='', $method='')
    {
        return $this->_factory($code, self::NOTICE, $class, $method);
    }

    public function success($code, $class='', $method='')
    {
        return $this->_factory($code, self::SUCCESS, $class, $method);
    }
}
