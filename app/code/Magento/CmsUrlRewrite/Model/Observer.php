<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CmsUrlRewrite\Model;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\CmsUrlRewrite\Service\V1\CmsPageUrlGeneratorInterface;
use Magento\UrlRedirect\Service\V1\UrlSaveInterface;
use Magento\Framework\Model\Exception;

class Observer
{
    /**
     * @var CmsPageUrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \Magento\UrlRedirect\Service\V1\UrlSaveInterface
     */
    protected $urlSave;

    /**
     * @param CmsPageUrlGeneratorInterface $urlGenerator
     * @param UrlSaveInterface $urlSave
     */
    public function __construct(CmsPageUrlGeneratorInterface $urlGenerator, UrlSaveInterface $urlSave)
    {
        $this->urlGenerator = $urlGenerator;
        $this->urlSave = $urlSave;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws Exception|\Exception
     */
    public function processUrlRewriteSaving(EventObserver $observer)
    {
        /** @var $cmsPage \Magento\Cms\Model\Page */
        $cmsPage = $observer->getEvent()->getObject();
        if ($cmsPage->getOrigData('identifier') !== $cmsPage->getData('identifier')) {
            $urls = $this->urlGenerator->generate($cmsPage);
            try {
                $this->urlSave->save($urls);
            } catch (\Exception $e) {
                if ($e->getCode() === 23000) { // Integrity constraint violation: 1062 Duplicate entry
                    throw new Exception(__('A page URL key for specified store already exists.'));
                }
                throw $e;
            }
        }
    }
}
