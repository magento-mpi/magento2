<?php
/**
 * Core cookie model
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Cookie
{
    const COOKIE_NAME = 'magenta';

    protected $_id = null;

    public function __construct()
    {
        if (isset($_COOKIE[self::COOKIE_NAME ])) {
            $this->_id = $_COOKIE[self::COOKIE_NAME];
        }
        else {
            $this->_id = $this->randomSequence();
            setcookie(self::COOKIE_NAME, $this->_id, time()+60*60*24*30, '/');
        }
    }

    public function getId()
    {
        return $this->_id;
    }

    public function randomSequence($length=32)
    {
        $id = '';
        $par = array();
        $char = array_merge(range('a','z'),range(0,9));
        $charLen = count($char)-1;
        for ($i=0;$i<$length;$i++){
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id.$char[$disc];
        }
        return $id;
    }

    public function set($cookieName, $value, $period=null)
    {
        if( !isset($period) ) {
            $period = 3600 * 24 * 365;
        }
        $expire = time() + $period;
        $this->delete($cookieName);
        setcookie($cookieName, $value, $expire, '/');
        return $this;
    }

    public function get($cookieName)
    {
        if( isset($_COOKIE[$cookieName]) ) {
            return $_COOKIE[$cookieName];
        } else {
            return false;
        }
    }

    public function delete($cookieName)
    {
        setcookie($cookieName, '', (time() - 3600) );
        return $this;
    }
}