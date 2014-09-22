<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Zend\Mvc\I18n\Translator;
use Zend\Mvc\MvcEvent;

class Location
{
    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function onChangeLocation(MvcEvent $e)
    {
        $locale = $this->translator->getLocale();
        $this->translator->setLocale($e->getRouteMatch()->getParam('lang'));
        $this->translator->setFallbackLocale($locale);
    }

    public function getLocationCode()
    {
        return substr($this->translator->getLocale(), 0, 5);
    }
}