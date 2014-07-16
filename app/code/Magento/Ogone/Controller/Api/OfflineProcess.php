<?php
/**
 * Action to process Ogone offline data
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Controller\Api;

class OfflineProcess extends \Magento\Ogone\Controller\Api
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->_validateOgoneData()) {
            $this->getResponse()->setHeader("Status", "404 Not Found");
            return false;
        }
        $this->_ogoneProcess();
    }
}
