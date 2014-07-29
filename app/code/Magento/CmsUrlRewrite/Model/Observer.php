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
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Framework\Model\Exception;

class Observer
{
    /**
     * @var CmsPageUrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @param CmsPageUrlGeneratorInterface $urlGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(CmsPageUrlGeneratorInterface $urlGenerator, UrlPersistInterface $urlPersist)
    {
        $this->urlGenerator = $urlGenerator;
        $this->urlPersist = $urlPersist;
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
            // TODO: fix service parameter
            $urls = $this->urlGenerator->generate($cmsPage);
            try {
                $this->urlPersist->replace($urls);
            } catch (\Exception $e) {
                if ($e->getCode() === 23000) { // Integrity constraint violation: 1062 Duplicate entry
                    throw new Exception(__('A page URL key for specified store already exists.'));
                }
                throw $e;
            }
        }
    }
}
