@vmpro_api.api_client_factory
=============================

This service is the Video Manager Pro API client's factory, automatically tailored
for which version of the Guzzle HTTP client is present in your project (or latest
version if your product does not specify it as a dependency.)

Under normal circumstances where you configure your Video Manager Pro credentials
statically in your ``parameters.yml`` and ``app/config/config.yml`` you will probably
not need to access this service directly, but if you need to instantiate a client with
a dynamic set of credentials, you can easily do this:

.. code-block:: php

    <?php

    // ..

    class MyService
    {
        public function __construct($apiClientFactory, $apiBaseUrl)
        {
            $username = 'dynamic';
            $password = 'dynamic too';
            $videoManagerId = 99;

            $client = $apiClientFactory->createSimple($baseUrl, new ApiCredentials($username, $password));
            $client->getChannels($videoManagerId);
        }
    }
