<?php
class Enterprise_Permissions_Model_Validator
{
    public function systemConfigEdit($observer)
    {
        if( !Mage::helper('permissions')->isSuperAdmin() ) {
            $website = $observer->getEvent()->getControllerAction()->getRequest()->getParam('website');
            $store = $observer->getEvent()->getControllerAction()->getRequest()->getParam('store');
            if( !Mage::helper('permissions')->hasConfigAccess($website, $store) ) {
                if( $url = Mage::helper('permissions')->getConfigRedirectUrl() ) {
                    $observer->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
                } else {
                    $this->_raiseDenied($observer);
                }
            }
        }

        return $this;
    }

    public function systemConfigSave($observer)
    {
        if( Mage::helper('permissions')->isSuperAdmin() ) {
            return $this;
        }

        $request = $observer->getEvent()->getControllerAction()->getRequest();

        if( $request->getParam('store') && !Mage::helper('permissions')->hasConfigAccess(null, $request->getParam('store')) ) {
            $this->_raiseDenied($observer);
            return $this;
        }

        if( $request->getParam('website') && !Mage::helper('permissions')->hasConfigAccess($request->getParam('website'), null) ) {
            $this->_raiseDenied($observer);
            return $this;
        }

        return $this;
    }

    protected function _raiseDenied($observer)
    {
        $observer->getEvent()->getControllerAction()->getResponse()->setRedirect(Mage::getUrl('adminhtml/index/denied'));
    }
}