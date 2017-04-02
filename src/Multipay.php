<?php

namespace Multipay;

class Multipay
{
  protected $code;
  protected $path;
  protected $token;
  protected $timeout;

  /**
   * Constructor
   * @param string $code
   * @param string $token
   */
  public function __construct($code = NULL, $token = NULL)
  {
    if(is_null($code) || is_null($token) ){
      trigger_error('You must provide the given code and token.', E_USER_ERROR);
    }

    if (!extension_loaded('curl')) {
      trigger_error('Extension CURL is not loaded.', E_USER_ERROR);
    }

    $this->code = $code;
    $this->token = $token;
    $this->timeout = 60;
  }

  /**
   * Generate a transaction
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  public function generate($data)
  {

    try {
        $ch = $this->initCurlHandler($this->timeout, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://institution.multipay.ph/api/v1/transactions/create');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::buildData($data));
        $return = json_decode(curl_exec($ch), true);
        curl_close($ch);
    } catch (Exception $e) {
        $return = null;
    }
    return $return;
  }


  /**
   * [initCurlHandler description]
   * @param  [type] $timeout [description]
   * @param  [type] $mode    [description]
   * @return [type]          [description]
   */
  private function initCurlHandler($timeout, $mode)
  {
    $multipayHeader = array(
        'X-MultiPay-Code: '.$this->code,
        'X-MultiPay-Token: '.$this->token
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $multipayHeader);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $mode);
    return $ch;
  }

  private function buildData($payload)
  {
      $data = array_merge($payload, [
        'digest' => self::generateDigest($payload)
      ]);
      return $data;
  }

  private function generateDigest($payload)
  {
    $payload = array_merge($payload, [
      'code' => $this->code,
      'token' => $this->token
    ]);

    ksort($payload);
    return sha1(implode(':', $payload));
  }
}
