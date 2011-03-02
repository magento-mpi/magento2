<?php
class VarienGateway
{
	/*
	 * Channel names
	 */
	const CHANNEL_CORE = 'core';
	const CHANNEL_COMMUNITY = 'community';
	
	/*
	 * Action names
	 */
	const ACTION_CREATE_PACKAGE = 'createPackage';
	const ACTION_PACKAGE_EXISTS = 'packageExists';
	const ACTION_DELETE_PACKAGE = 'deletePackage';
	const ACTION_UPDATE_PACKAGE = 'updatePackage';
	const ACTION_SET_PACKAGE_OWNER = 'setPackageOwner';
	const ACTION_ADD_MAINTAINER = 'addMaintainer';
	const ACTION_UPLOAD_RELEASE = 'uploadRelease';
	const ACTION_DELETE_RELEASE = 'deleteRelease';
	const ACTION_VALIDATE_RELEASE = 'validateRelease';
	const ACTION_MAINTAINER_EXISTS = 'maintainerExists';
	
	private $_userAuthId    = '973e94dac41ba9d1fc9f5b818505610a';
	private $_userSecretKey = '01ead244abefd7219160773fe28a1a17';

	/**
	 * Gateway url
	 * 
	 * @var string
	 */
	protected $_gatewayUrl;
	private $_httpClient;
	
	public function __construct($gatewayUrl = null, $userAuthId = null, $userSecretKey = null)
	{
	    if (is_null($gatewayUrl)) {
	        global $PREFS;
	        $gatewayUrl = $PREFS->ini('varienChannelUrl');
	    }
	    if (!is_null($userAuthId)) {
	        $this->_userAuthId=$userAuthId;
	    }
	    if (!is_null($userSecretKey)) {
	        $this->_userSecretKey=$userSecretKey;
	    }
		$this->_gatewayUrl = $gatewayUrl;
		$this->_httpClient = 'curl';
	}
	
	/**
	 * Create package
	 * 
	 * @param array $data must contain following parameters:
	 *     name
	 *     license
	 *     licenseuri
	 *     description
	 *     summary
	 *     channel
	 * @return string | boolean
	 */
    public function createPackage($data)
    {
    	return $this->_submitData(self::ACTION_CREATE_PACKAGE, $data);
    }
    
    
    /**
     * Delete package
     * 
     * @param string $pkgName
     * @return boolean
     */
    public function deletePackage($pkgName)
    {
    	return $this->_submitData(
    		self::ACTION_DELETE_PACKAGE, 
    		array(
    			'package' => $pkgName, 
    			'channel' => self::CHANNEL_CORE
    		)
    	);
    }
    
    /**
     * Update package
     * 
     * @param array $data - channel, license, licenseuri, description, summary
     * @param string $pkgName
     * @return boolean
     */
    public function updatePackage($data, $pkgName)
    {
    	$data['name']    = $pkgName;
    	return $this->_submitData(self::ACTION_UPDATE_PACKAGE, $data);
    }
    
    /**
     * Check if package exists
     * @param string $pkgName
     * @param string $channel
     * @return boolean
     */
    public function isPackageExists($pkgName, $channel = self::CHANNEL_CORE)
    {
    	$result = $this->_submitData(
    	   self::ACTION_PACKAGE_EXISTS,
    	   array(
    	       'package' => $pkgName,
    	       'channel' => $channel
    	   )
    	);
    	return empty($result->error);
    }
    
    /**
     * Upload package release
     * 
     * @param string $pkgFile
     * @param string $channel
     * @return stdClass
     */
    public function uploadRelease($pkgFile, $channel)
    {
    	$files = array(
            array('file', $pkgFile)
        );
    	$data = array(
    		'channel'=> $channel
    	);
		return $this->_submitData(self::ACTION_UPLOAD_RELEASE, $data, $files);
    }
    
    /**
     * Validate release (check package and return information about it)
     * 
     * @param string $pkgName
     * @param string $channel
     * @return stdClass
     */
    public function validateRelease($pkgFile, $channel)
    {
    	$files = array(
            array('file', $pkgFile),
        );
        $data = array(
            'channel'=> $channel
        );
        return $this->_submitData(self::ACTION_VALIDATE_RELEASE, $data, $files);
    }
    
    /**
     * Delete package release
     * 
     * @param string $pkgName
     * @param string $relVersion
     * @param string $channel
     * @return stdClass
     */
    public function deleteRelease($pkgName, $relVersion, $channel)
    {
    	return $this->_submitData(
	    	self::ACTION_DELETE_RELEASE,
	    	array(
		    	'channel' => $channel,
		    	'package' => $pkgName,
		    	'release' => $relVersion
	    	)
    	);
    }
    

    /**
     * Add new maintainer
     * 
     * @param array $data - channel, login, password, email, fullname
     * @return boolean
     */
    public function addMaintainer($data)
    {
    	return $this->_submitData(
    	   self::ACTION_ADD_MAINTAINER,
    	   $data
    	);
    }
    
    /**
     * Check if maintainer exists
     * 
     * @param array $data - channel, login
     * @return boolean
     */
    public function maintainerExists($data)
    {
    	$existsRes = $this->_submitData(
    	   self::ACTION_MAINTAINER_EXISTS,
    	   $data
    	);
    	return empty($existsRes->error);
    }
    
    
    /**
     * Add package maintainer
     * 
     * @param array $data - channel, username
     * @param string $pkgName
     * @return unknown_type
     */
    public function setPackageOwner($data, $pkgName)
    {
    	$data['package'] = $pkgName;
    	return $this->_submitData(
    	   self::ACTION_SET_PACKAGE_OWNER,
    	   $data
    	);
    }
    
    public function userLogin($uname=null, $upass=null)
    {
    	return true;
    }
    
    
    /**
     * authorization
     */
    public function setUrl()
    {
    	return $this;
    }
    
    public function setJsonUrl()
    {
    	return $this;
    }
    
    public function setUser($userAuthId)
    {
    	$this->_userAuthId = $userAuthId;
    	return $this;
    }
    
    public function setPasswd($userSecretKey)
    {
    	$this->_userSecretKey = $userSecretKey;
    	return $this;
    }
    
    /**
     * return auth signature
     * @param $method str use constants declared in this class!!!
     * @return  str
     */
    private function getSignature($method)
    {
    	return sha1($this->_gatewayUrl . $method . $this->_userSecretKey);
    }
    
    /**
     * makes request to api
     * @param $methodName str
     * @param $data array
     * @param $files array(array(paramname, filedata),...)
     * @return stdClass
     */
    private function _submitData($methodName, $data, $files=null)
    {
    	$authHeader = array('Auth:' . sha1($this->_userAuthId) . ':' . $this->getSignature($methodName));
        $data['act'] = $methodName;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_gatewayUrl );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        
        if (isset($authHeader)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeader);
        }

        if (isset($files)) {
            foreach ($files as $key => $value) {
                $data += array($value[0] => '@'.$value[1]);
            }
        }
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        $response = curl_exec( $ch );
        $result = json_decode($response);
        return $result;
    }
    
    /**
     * Init channel user
     * 
     * @param boolean $useRootCredentials
     * @return VarienGateway
     */
    public function initUser($useRootCredentials = false)
    {
        global $PREFS, $SESS;
        
        $authId    = '';
        $secretKey = '';
        if (true === $useRootCredentials) {
            $authId    = $PREFS->ini('varienChannelAdminLogin');
            $secretKey = $PREFS->ini('varienChannelAdminPass');
        } else {
            if (empty($SESS->userdata['channel_auth_id'])
                || empty($SESS->userdata['channel_secret_key'])
            ) {
                // Get current user credentials
                $credentials = Varien_Member::getChannelCredentials($SESS->userdata('member_id'));
                $authId = $SESS->userdata['channel_auth_id'] = $credentials['channel_auth_id'];
                $secretKey = $SESS->userdata['channel_secret_key'] = $credentials['channel_secret_key'];
            } else {
            	$authId = $SESS->userdata['channel_auth_id'];
                $secretKey = $SESS->userdata['channel_secret_key'];
            }
        }

        $this->setUser($authId)
             ->setPasswd($secretKey);
             
        return $this;
    }
}