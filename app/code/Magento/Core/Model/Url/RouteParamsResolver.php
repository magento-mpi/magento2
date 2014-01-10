<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Url;


class RouteParamsResolver  extends \Magento\Object implements \Magento\Url\RouteParamsResolverInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_request = $request;
        $this->_storeConfig = $storeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteParams(array $data, $unsetOldParams = true)
    {
        if (isset($data['_type'])) {
            $this->setType($data['_type']);
            unset($data['_type']);
        }

        if (isset($data['_scope'])) {
            $this->setScope($data['_scope']);
            unset($data['_scope']);
        }

        if (isset($data['_forced_secure'])) {
            $this->setSecure((bool)$data['_forced_secure']);
            $this->setSecureIsForced(true);
            unset($data['_forced_secure']);
        } elseif (isset($data['_secure'])) {
            $this->setSecure((bool)$data['_secure']);
            unset($data['_secure']);
        }

        if (isset($data['_absolute'])) {
            unset($data['_absolute']);
        }

        if ($unsetOldParams) {
            $this->unsetData('route_params');
        }

        if (isset($data['_current'])) {
            if (is_array($data['_current'])) {
                foreach ($data['_current'] as $key) {
                    if (array_key_exists($key, $data) || !$this->_request->getUserParam($key)) {
                        continue;
                    }
                    $data[$key] = $this->_request->getUserParam($key);
                }
            } elseif ($data['_current']) {
                foreach ($this->_request->getUserParams() as $key => $value) {
                    if (array_key_exists($key, $data) || $this->getRouteParam($key)) {
                        continue;
                    }
                    $data[$key] = $value;
                }
                foreach ($this->_request->getQuery() as $key => $value) {
                    $this->setQueryParam($key, $value);
                }
            }
            unset($data['_current']);
        }

        if (isset($data['_use_rewrite'])) {
            unset($data['_use_rewrite']);
        }

        if (isset($data['_scope_to_url']) && (bool)$data['_scope_to_url'] === true) {
            if (!$this->_storeConfig->getConfig(\Magento\Core\Model\Store::XML_PATH_STORE_IN_URL, $this->getScope())
                && !$this->_storeManager->hasSingleStore()
            ) {
                $this->setQueryParam('___store', $this->getScope()->getCode());
            }
        }
        unset($data['_scope_to_url']);

        foreach ($data as $key => $value) {
            $this->setRouteParam($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteParam($key, $data)
    {
        $params = $this->_getData('route_params');
        if (isset($params[$key]) && $params[$key] == $data) {
            return $this;
        }
        $params[$key] = $data;
        $this->unsetData('route_path');
        return $this->setData('route_params', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParams()
    {
        return $this->_getData('route_params');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParam($key)
    {
        return $this->getData('route_params', $key);
    }
}
