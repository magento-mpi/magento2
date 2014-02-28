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
     * @param \Magento\Backend\App\AbstractAction $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Backend\App\AbstractAction $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $key = $request->getPost('massaction_prepare_key');
        if ($key) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $proceed($request);
    }
} 
