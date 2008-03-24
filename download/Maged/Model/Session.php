<?php

class Maged_Model_Session extends Maged_Model
{
    public function start()
    {
        session_start();
        return $this;
    }

    public function authenticate()
    {
        if ($this->getUserId()) {
            return $this;
        }

        $ds = DIRECTORY_SEPARATOR;
        
        $mageFilename = $this->controller()->getMageDir().$ds.'app'.$ds.'Mage.php';
        $varFilename = $this->controller()->getMageDir().$ds.'lib'.$ds.'Varien'.$ds.'Profiler.php';
        
        if (!file_exists($mageFilename) || !file_exists($varFilename)) {
            return $this;
        }

        try {
            #$displayErrors = ini_get('display_errors');
            #ini_set('display_errors', 0);
            
            include_once $mageFilename;
            Mage::app('admin');

            if (!Mage::app()->isInstalled()) {
                return $this;
            }
    
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $this->controller()->setAction('login');
                return $this;
            }
    
            $user = Mage::getModel('admin/user');
    
            if (method_exists($user, 'authenticate')) {
                $auth = $user->authenticate($_POST['username'], $_POST['password']);
            } else { // 0.9.17740
                $authAdapter = $user->getResource()->getAuthAdapter();
                $authAdapter->setIdentity($_POST['username'])->setCredential($_POST['password']);
                $resultCode = $authAdapter->authenticate()->getCode();
    
                $auth = Zend_Auth_Result::SUCCESS===$resultCode;
            }
    
            if (!$auth) {
                $this->addMessage('error', 'Invalid user name or password');
                $this->setAction('login');
                return $this;
            }
    
            $_SESSION['user_id'] = $user->getId();
            
            #ini_set('display_errors', $displayErrors);
            
        } catch (Exception $e) {
            
            $this->addMessage('error', $e->getMessage());
            
        }

        $this->controller()
            ->redirect($this->controller()->url($this->controller()->getAction()).'&loggedin', true);
    }

    public function getUserId()
    {
        if (!isset($this->_data['user_id'])) {
            $this->_data['user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;
        }
        return $this->get('user_id');
    }

    public function addMessage($type, $msg)
    {
        $msgs = $this->getMessages(false);
        $msgs[$type][] = $msg;
        $_SESSION['messages'] = $msgs;
        return $this;
    }

    public function getMessages($clear = true)
    {
        $msgs = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
        if ($clear) {
            unset($_SESSION['messages']);
        }
        return $msgs;
    }
}
