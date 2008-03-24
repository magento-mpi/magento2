<?php

class Maged_View
{
    protected $_data = array();

    public function __construct()
    {

    }

    public function controller()
    {
        return Maged_Controller::singleton();
    }

    public function url($action='', $params=array())
    {
        return $this->controller()->url($action, $params);
    }

    public function template($name)
    {
        ob_start();
        include $this->controller()->filepath('template/'.$name);
        return ob_get_clean();
    }

    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __($string)
    {
        return $string;
    }

    public function getNavLinkParams($action)
    {
        $params = 'href="'.$this->url($action).'"';
        if ($this->controller()->getAction()==$action) {
            $params .= ' class="active"';
        }
        return $params;
    }
}
