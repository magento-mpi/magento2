<?php

class Mage_Core_Model_Email_Template_Filter extends Varien_Filter_Template
{
	public function blockDirective($construction)
	{
		$blockParameters = $this->_getIncludeParameters($construction[2]);
		$layout = Mage::registry('action')->getLayout();

		if (isset($blockParameters['type'])) {
    		$type = $blockParameters['type'];

    		$block = $layout->createBlock($type);
    		if (!$block) {
    		    return '';
    		}
    		if (!empty($blockParameters['template'])) {
    			$block->setTemplate($blockParameters['template']);
    		}

    		$block->addData($blockParameters);

    		return $block->toHtml();
		}

		if (isset($blockParameters['id'])) {
		    $block = $layout->createBlock('cms/block');

		    if (!$block) {
		        return '';
		    }

		    $block
		        ->setBlockId($blockParameters['id'])
		        ->setBlockParams($blockParameters);

		    return $block->toHtml();
		}

        return '';
	}

	protected function _getBlockParameters($value)
	{
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);

        return $tokenizer->tokenize();
	}

	public function skinDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);

		$url = Mage::getDesign()->getSkinUrl($params['url']);

		return $url;
	}

	public function storeDirective($construction)
	{
    	$params = $this->_getIncludeParameters($construction[2]);

    	$path = $params['url'];
    	unset($params['url']);

    	$url = Mage::getUrl($path, $params);

    	return $url;
	}

}