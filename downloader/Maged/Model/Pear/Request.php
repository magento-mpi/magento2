<?php

class Maged_Model_Pear_Request extends Maged_Model
{
    protected function _construct()
    {
        parent::_construct();
        $this->set('success_callback', 'parent.onSuccess()');
        $this->set('failure_callback', 'parent.onFailure()');
    }
}
