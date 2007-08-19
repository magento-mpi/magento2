<?php

class Mage_GoogleAnalytics_Block_Urchin extends Mage_Core_Block_Text 
{
	public function getScriptUrl()
	{
		if (empty($_SERVER['HTTPS'])) {
			return 'http://www.google-analytics.com/urchin.js';
		} else {
			return 'https://ssl.google-analytics.com/urchin.js';
		}
	}
	
	public function getAccount()
	{
		if (!$this->hasData('account')) {
			$this->setAccount(Mage::getStoreConfig('web_track/google/urchin_account'));
		}
		return $this->getData('account');
	}
	
	public function getPageName()
	{
		if (!$this->hasData('page_name')) {
			$this->setPageName($this->getRequest()->getPathInfo());
		}
		return $this->getData('page_name');
	}
	
	public function toHtml()
	{
		if (!Mage::getStoreConfig('web_track/google/urchin_enable')) {
			return '';
		}
		
		$this->addText('
<!-- BEGIN GOOGLE ANALYTICS CODE -->
<script src="'.$this->getScriptUrl().'" type="text/javascript"></script> 
<script type="text/javascript">
_uacct="'.$this->getAccount().'";
urchinTracker("'.$this->getPageName().'");
</script>
<!-- END GOOGLE ANALYTICS CODE -->
		');
		
		return parent::toHtml();
	}
}