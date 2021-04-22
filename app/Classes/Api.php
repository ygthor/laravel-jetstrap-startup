<?php

namespace App\Classes;

class Api
{
    /**
     *  CALL API FUNCTION
     * - callPostFunction (use POST for CRUD)
     * - callGetFunction (use GET for CRUD)
     * - callApiFunction (default is GET)
     */
    protected function curlGet($params)
    {
        $params['method'] = 'get';
        return $this->curl($params);
    }
    protected function curlPost($params)
    {
        $params['method'] = 'post';
        return $this->curl($params);
    }
    protected function curlPut($params)
    {
        $params['method'] = 'put';
        return $this->curl($params);
    }
    private function curl($params)
    {
        $url = $params['url'];
        $method = $params['method'];
        $authorization = $params['authorization'];
        $body = $params['body'] ?? [];

        $api_request_url = $url;

        $ch = curl_init();

        $header = [
            'Authorization: ' . $authorization
        ];

        // pass header variable in curl method
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $api_request_url);
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else if ($method == 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);


        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // 200 mean ok
        curl_close($ch);

        if ($httpCode == '200') {
            return [
                'success' => 1,
                'data' => json_decode($result, true)
            ];
        } else {
            return [
                'success' => 0,
                'data' => json_decode($result, true)
            ];
            dump($result);
            dd('Api Error, Please retry later or contact dev');
            return false;
        }
    }
}
