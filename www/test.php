<?php
require_once 'Zend/Acl.php';

$acl = new Zend_Acl();

require_once 'Zend/Acl/Role.php';

$roleGuest = new Zend_Acl_Role('guest');
$acl->addRole($roleGuest);
$acl->addRole(new Zend_Acl_Role('staff'), $roleGuest);
$acl->addRole(new Zend_Acl_Role('editor'), 'staff');
$acl->addRole(new Zend_Acl_Role('administrator'));

// Guest may only view content
$acl->allow($roleGuest, null, 'view');

/* alternatively, the above could be written:
$acl->allow('guest', null, 'view');
//*/

// Staff inherits view privilege from guest, but also needs additional privileges
$acl->allow('staff', null, array('edit', 'submit', 'revise'));

// Editor inherits view, edit, submit, and revise privileges from staff,
// but also needs additional privileges
$acl->allow('editor', null, array('publish', 'archive', 'delete'));

// Administrator inherits nothing, but is allowed all privileges
$acl->allow('administrator');

// The new marketing group inherits permissions from staff
$acl->addRole(new Zend_Acl_Role('marketing'), 'staff');


// Create Resources for the rules
require_once 'Zend/Acl/Resource.php';
$acl->add(new Zend_Acl_Resource('newsletter'));           // newsletter
$acl->add(new Zend_Acl_Resource('news'));                 // news
$acl->add(new Zend_Acl_Resource('latest'), 'news');       // latest news
$acl->add(new Zend_Acl_Resource('announcement'), 'news'); // announcement news

// Marketing must be able to publish and archive newsletters and the latest news
$acl->allow('marketing', array('newsletter', 'latest'), array('publish', 'archive'));

// Staff (and marketing, by inheritance), are denied permission to revise the latest news
$acl->deny('staff', 'latest', 'revise');

// Everyone (including administrators) are denied permission to archive news announcements
$acl->deny(null, 'announcement', 'archive');

require_once 'Zend/Acl/Assert/Interface.php';

class CleanIPAssertion implements Zend_Acl_Assert_Interface
{
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        return $this->_isCleanIP($_SERVER['REMOTE_ADDR']);
    }

    protected function _isCleanIP($ip)
    {
        // ...
    }
}

$acl->allow(null, null, null, new CleanIPAssertion());

echo "<pre>";
print_r($acl);