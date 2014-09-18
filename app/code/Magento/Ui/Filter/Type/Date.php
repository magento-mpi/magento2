<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter\Type;

use\Magento\Ui\Filter\View;
use Magento\Ui\ConfigurationFactory;

use Magento\Ui\Filter\FilterPool;
use Magento\Ui\Context;
use Magento\Framework\LocaleInterface;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class Date
 */
class Date extends View
{
    /**
     * Timezone library
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Locale resolver
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * Constructor
     *
     * @param FilterPool $filterPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigurationFactory $configurationFactory
     * @param ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        FilterPool $filterPool,
        Context $renderContext,
        TemplateContext $context,
        ContentTypeFactory $contentTypeFactory,
        ConfigurationFactory $configurationFactory,
        ResolverInterface $localeResolver,
        array $data = []
    ) {
        $this->localeDate = $context->getLocaleDate();
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeResolver = $localeResolver;
        parent::__construct($filterPool, $renderContext, $context, $contentTypeFactory, $configurationFactory, $data);
    }

    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        return $this->convertValue($value);
    }

    /**
     * Convert value
     *
     * @param array|string $value
     * @return array|null
     */
    protected function convertValue($value)
    {
        if (!empty($value['from']) || !empty($value['to'])) {
            $locale = $this->localeResolver->getLocale();
            if (!empty($value['from'])) {
                $value['orig_from'] = $value['from'];
                $value['from'] = $this->convertDate(strtotime($value['from']), $locale);
            }
            if (!empty($value['to'])) {
                $value['orig_to'] = $value['to'];
                $value['to'] = $this->convertDate(strtotime($value['to']), $locale);
            }
            $value['datetime'] = true;
            $value['locale'] = $locale->toString();
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param int $date
     * @param LocaleInterface $locale
     * @return \Magento\Framework\Stdlib\DateTime\Date|null
     */
    protected function convertDate($date, LocaleInterface $locale)
    {
        try {
            $dateObj = $this->localeDate->date(null, null, $locale, false);

            //set default timezone for store (admin)
            $dateObj->setTimezone(
                $this->scopeConfig->getValue(
                    $this->localeDate->getDefaultTimezonePath(),
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            //set beginning of day
            $dateObj->setHour(00);
            $dateObj->setMinute(00);
            $dateObj->setSecond(00);

            //set date with applying timezone of store
            $dateObj->set($date, null, $locale);

            //convert store date to default date in UTC timezone without DST
            $dateObj->setTimezone(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::DEFAULT_TIMEZONE);

            return $dateObj;
        } catch (\Exception $e) {
            return null;
        }
    }
}
