<?php
/**
 * Message model
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Message
{
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const SUCCESS   = 'success';
    
    public function __construct()
    {
        
    }
    
    protected function _factory($code, $type, $class='', $method='')
    {
        switch ($type) {
            case self::ERROR :
                $message = new Mage_Core_Model_Message_Error($code);
                break;
            case self::WARNING :
                $message = new Mage_Core_Model_Message_Warning($code);
                break;
            case self::SUCCESS :
                $message = new Mage_Core_Model_Message_Success($code);
                break;
            default:
                $message = new Mage_Core_Model_Message_Notice($code);
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