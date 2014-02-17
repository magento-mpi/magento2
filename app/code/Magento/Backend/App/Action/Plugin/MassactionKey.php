<?php
/**
 * Massaction key processor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Action\Plugin;


class MassactionKey
{
    /**
     * Process massaction key
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $request = $arguments[0];
        $key = $request->getPost('massaction_prepare_key');
        if ($key) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $invocationChain->proceed($arguments);
    }
} 
