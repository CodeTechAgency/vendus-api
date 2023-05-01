<?php

namespace CodeTech\VendusApi;

class Api
{
    private const BASE_URI = 'https://www.vendus.pt/ws/v1.1/';

    /**
     * The Api key.
     *
     * @var string
     */
    public $apiKey;

    /**
     * The HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    public $httpClient;

    /**
     * The error messages.
     *
     * @var array
     */
    private $errors = [];

    /**
     * VendusApi constructor.
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;

        $this->initHttpClient();
    }

    /**
     * Initializes the HTTP client.
     */
    private function initHttpClient()
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * Returns the default query parameters that must be sent on every request.
     *
     * @return array
     */
    public function getDefaultQueryParams(): array
    {
        return [
            'api_key' => $this->apiKey,
        ];
    }

    /**
     * Get the clients endpoint.
     *
     * @return Endpoint
     */
    public function clients(): Endpoint
    {
        $this->uri = 'clients';

        return new Endpoint($this);
    }

    /**
     * Get the products endpoint.
     *
     * @return Endpoint
     */
    public function products(): Endpoint
    {
        $this->uri = 'products';

        return new Endpoint($this);
    }

    /**
     * Get the products units endpoint.
     *
     * @return Endpoint
     */
    public function units(): Endpoint
    {
        $this->uri = 'products/units';

        return new Endpoint($this);
    }

    /**
     * Get the documents endpoint.
     *
     * @return Endpoint
     */
    public function documents(): Endpoint
    {
        $this->uri = 'documents';

        return new Endpoint($this);
    }

    /**
     * Get the payment methods endpoint.
     *
     * @return Endpoint
     */
    public function paymentMethods(): Endpoint
    {
        $this->uri = 'documents/paymentmethods';

        return new Endpoint($this);
    }

    /**
     * Get the errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set the errors.
     *
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
