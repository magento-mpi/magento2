<?php

class Maged_Model_Config extends Maged_Model
{
    public function saveConfigPost($p)
    {
        $this->set('preferred_state', $p['preferred_state']);
        $this->set('mage_dir', $p['mage_dir']);
        $this->save();
        return $this;
    }
    
    public function getFilename()
    {
        return $this->controller()->filepath('config.ini');
    }
    
    public function load()
    {
        $rows = file($this->getFilename());
        if (!$rows) {
            return $this;
        }
        foreach ($rows as $row) {
            $arr = explode('=', $row, 2);
            if (count($arr)!==2) {
                continue;
            }
            $key = trim($arr[0]);
            $value = trim($arr[1], " \t\"'");
            if (!$key || $key[0]=='#' || $key[0]==';') {
                continue;
            }
            $this->set($key, $value);
        }
        return $this;
    }
    
    public function save()
    {
        if (!is_writable($this->getFilename())) {
            $this->controller()->session()
                ->addMessage('error', 'Please make sure all Magento folders are writable');
        }
        $fp = fopen($this->getFilename(), 'w');
        foreach ($this->_data as $k=>$v) {
            fwrite($fp, $k.'='.$v."\n");
        }
        fclose($fp);
        return $this;
    }
    
    public function validateEnvironment()
    {
        $ok = true;
        $ctrl = $this->controller();
        $writable = is_writable($ctrl->getMageDir())
            && is_writable($ctrl->filepath())
            && is_writable($ctrl->filepath('config.ini'))
            && is_writable($ctrl->filepath('pearlib/pear.ini'))
            && is_writable($ctrl->filepath('pearlib/php'))
        ;
        if (!$writable) {
            $ok = false;
            $ctrl->session()->addMessage('error', 'Please make sure all Magento folders are writable');
        }
        
        return $ok;
    }
}
