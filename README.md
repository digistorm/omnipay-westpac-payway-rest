# omnipay-westpac-payway-rest

Note: When instantiating the gateway, it needs to be provided with an Omnipay HTTP Client that is injected with a Guzzle client configured to implement Curl TLS 1.2.

e.g.

```php

use Omnipay\WestpacPaywayRest\Gateway;
use Omnipay\Common\Http\Client as OmnipayHttpClient;
use GuzzleHttp\Client as GuzzleHttpClient;

$curlConfig = [
    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
];
$guzzleClient = new GuzzleHttpClient(['curl' => $curlConfig]);
$omnipayClient = new OmnipayHttpClient($guzzleClient);
$gateway = new Gateway($omnipayClient);
```