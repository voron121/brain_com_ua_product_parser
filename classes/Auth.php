<?php

namespace App;

use GuzzleHttp\Client;
use Exception;

class Auth
{
    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSessionToken(): string
    {
        $config  = new Config();
        $httpClient = new Client();
        $response = $httpClient->request(
            'POST',
            $config->apiAuthEndpoint,
            [
                'form_params' => [
                    'login' => $config->apiLogin,
                    'password' => md5($config->apiPassword)
                ]
            ]
        );
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Error getting SID: http code' . $response->getStatusCode() );
        }
        $responseBody = json_decode($response->getBody());
        if ($responseBody->status != 1) {
            throw new Exception('Error getting SID: ' . $responseBody->result );
        }
        return $responseBody->result;
    }
}