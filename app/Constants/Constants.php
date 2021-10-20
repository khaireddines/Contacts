<?php

namespace App\Constants;

class Constants
{
    const app_url = 'http://contacts.me';
    const authentication_uri = 'https://accounts.infusionsoft.com/app/oauth/authorize?client_id='.self::client_id.'&redirect_uri='.self::redirect_uri.'&response_type='.self::response_type;
    const accessToken_uri = 'https://api.infusionsoft.com/token';
    const email = 'nazran.info@gmail.com';
    const password = 'Nazrankeap@11';
    const client_id = 'kbC31cNX5fWckyocDsTtmhA7bQL0nXrg';
    const client_secret = '9wRhhb2nkSdLd1m5';
    const response_type = 'code';
    const redirect_uri = self::app_url.'/ApiAccessTokens';
    const grant_type = 'authorization_code';
    const api_uri = 'https://api.infusionsoft.com/crm/rest/v1';

    /** Do Not Touch This Logic */
    protected $API_TOKEN = '';
    protected $CODE = '';
    protected $SCOPE = '';

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->API_TOKEN;
    }

    /**
     * @param string $API_TOKEN
     */
    public function setApiToken($API_TOKEN)
    {
        $this->API_TOKEN = $API_TOKEN;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->CODE;
    }

    /**
     * @param string $CODE
     */
    public function setCode($CODE)
    {
        $this->CODE = $CODE;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->SCOPE;
    }

    /**
     * @param string $SCOPE
     */
    public function setScope($SCOPE)
    {
        $this->SCOPE = $SCOPE;
    }

    /** Do Not Touch This Logic */



}
