<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Class request
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Model_Connect_Request extends Maged_Model
{
    protected function _construct()
    {
        parent::_construct();
        $this->set('success_callback', 'clear_cache({success:parent.onSuccess, fail:parent.onFailure})');
        $this->set('failure_callback', 'parent.onFailure()');
    }
}
