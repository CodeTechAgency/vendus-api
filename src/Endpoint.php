<?php

namespace CodeTech\VendusApi;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Endpoint
{
    /**
     * The Vendus Api instance.
     *
     * @var Api
     */
    protected $api;

    /**
     * Endpoint constructor.
     *
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Find the specified resource.
     *
     * @param int $id
     * @param array $params
     * @return mixed|null
     */
    public function find(int $id, array $params = [])
    {
        $uri = sprintf('%s/%s', $this->api->uri, $id);

        try {
            $response = $this->api->httpClient->get(
                $uri,
                [
                    'query' => array_merge($this->api->getDefaultQueryParams(), $params),
                ]
            );
        } catch (GuzzleException $exception) {
            $this->handleException($exception);

            return null;
        }

        $resource = $this->getResponseContents($response);

        return $resource;
    }

    /**
     * Get a list of the specified resource.
     *
     * @param array $params
     * @return array
     */
    public function get(array $params = [])
    {
        try {
            $response = $this->api->httpClient->get(
                $this->api->uri,
                [
                    'query' => array_merge($this->api->getDefaultQueryParams(), $params),
                ]
            );
        } catch (GuzzleException $exception) {
            $this->handleException($exception);

            return [];
        }

        return $this->getResponseContents($response);
    }

    /**
     * Get a paginated list of the specified resource.
     *
     * @param array $params
     * @param int $page
     * @param int $perPAge
     * @return array
     */
    public function paginate(array $params = [], int $page, int $perPAge)
    {
        $params['page']     = $page;
        $params['per_page'] = $perPAge;

        $responseData = [
            'data' => [],
            'total' => 0,
        ];

        try {
            $response = $this->api->httpClient->get(
                $this->api->uri,
                [
                    'query' => array_merge($this->api->getDefaultQueryParams(), $params),
                ]
            );
        } catch (GuzzleException $exception) {
            $this->handleException($exception);

            return $responseData;
        }

        $responseData['data']  = $this->getResponseContents($response);
        $responseData['total'] = (int)$response->getHeaders()['X-Paginator-Items'][0] ?? 0;

        return $responseData;
    }

    /**
     * Creates a new resource.
     *
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function create(array $params = [])
    {
        try {
            $response = $this->api->httpClient->post(
                $this->api->uri,
                [
                    'query' => $this->api->getDefaultQueryParams(),
                    'form_params' => $params
                ]
            );
        } catch (GuzzleException $exception) {
            $this->handleException($exception);

            throw $exception;
        }

        return $this->getResponseContents($response);
    }

    /**
     * Updates the specified resource.
     *
     * @param int $id
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function update(int $id, array $params = [])
    {
        $uri = sprintf('%s/%s', $this->api->uri, $id);

        try {
            $response = $this->api->httpClient->patch(
                $uri,
                [
                    'query' => $this->api->getDefaultQueryParams(),
                    'form_params' => $params
                ]
            );
        } catch (GuzzleException $exception) {
            $this->handleException($exception);

            throw $exception;
        }

        return $this->getResponseContents($response);
    }

    /**
     * Returns the response body contents as JSON.
     *
     * @param ResponseInterface $response
     * @return mixed
     */
    private function getResponseContents(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Handles an exceptions by setting the error message on the Api object.
     *
     * @param \Exception $exception
     */
    private function handleException(\Exception $exception)
    {
        $contents = json_decode($exception->getResponse()->getBody()->getContents());

        $errors = $contents->errors ?? [];

        foreach ($errors as $key => $error) {
            $errors[$key] = $error->code . ': ' . $error->message;
        }

        $this->api->setErrors($errors);
    }
}
