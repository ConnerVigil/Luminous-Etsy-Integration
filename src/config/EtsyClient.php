<?php

namespace JoinLuminous\EtsyOms\config;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use JoinLuminous\OmsContracts\Exceptions\AccessDeniedException;
use JoinLuminous\OmsContracts\Exceptions\BadRequestException;
use JoinLuminous\OmsContracts\Exceptions\InvalidConfigurationException;
use JoinLuminous\OmsContracts\Exceptions\ResourceNotFoundException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class EtsyClient
{
    private Client $client;

    public function __construct(EtsyConfig $config)
    {
        $keyString = $config->keyString;
        $baseUrl = $config->baseUrl;

        $accessToken = ""; // TODO: Figure out where to get this

        $headers = [
            'x-api-key' => $keyString,
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => $headers
        ]);
    }

    /**
     * Sleep if rate limited
     *
     * @param ResponseInterface $responseInterface
     *
     * @return void
     */
    public function sleepIfRateLimited(ResponseInterface $responseInterface): void
    {
        $retryAfter = (int) $responseInterface->getHeader('Retry-After')[0] ?? 0;
        sleep($retryAfter);
    }

    /**
     * GET request
     *
     * @param string $endpoint
     * @param array $params
     * @param int $maxRetries
     *
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws InvalidConfigurationException
     * @throws ResourceNotFoundException
     */
    public function get(string $endpoint, array $params = [], int $maxRetries = 0): mixed
    {
        try {
            $options = [];
            if ($params) {
                $options['query'] = $params;
            }

            $response = $this->client->get($endpoint, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException | ServerException | RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 429 && $maxRetries < 3) {
                $this->sleepIfRateLimited($e->getResponse());
                return $this->get($endpoint, $params, $maxRetries + 1);
            }

            switch ($statusCode) {
                case 400:
                    throw new BadRequestException();
                case 403:
                    throw new AccessDeniedException();
                case 404:
                    throw new ResourceNotFoundException();
                case 422:
                    throw new InvalidConfigurationException();
                default:
                    $exceptionType = get_class($e);
                    $errorMessage = "$exceptionType: {$e->getMessage()}";
                    throw new RuntimeException($errorMessage, $e->getCode());
            }
        } catch (ConnectException $e) {
            throw new RuntimeException('Network error: ' . $e->getMessage(), $e->getCode());
        } catch (GuzzleException $e) {
            throw new RuntimeException('Guzzle error: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * POST request
     *
     * @param string $endpoint
     * @param array $payload
     *
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws InvalidConfigurationException
     * @throws ResourceNotFoundException
     */
    public function post(string $endpoint, array $payload = []): mixed
    {
        try {
            $response = $this->client->post($endpoint, ['json' => $payload]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException | ServerException | RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();

            switch ($statusCode) {
                case 400:
                    throw new BadRequestException();
                case 403:
                    throw new AccessDeniedException();
                case 404:
                    throw new ResourceNotFoundException();
                case 422:
                    throw new InvalidConfigurationException();
                default:
                    $exceptionType = get_class($e);
                    $errorMessage = "$exceptionType: {$e->getMessage()}";
                    throw new RuntimeException($errorMessage, $e->getCode());
            }
        } catch (ConnectException $e) {
            throw new RuntimeException('Network error: ' . $e->getMessage(), $e->getCode());
        } catch (GuzzleException $e) {
            throw new RuntimeException('GuzzleException: ' . $e->getMessage(), $e->getCode());
        }
    }


    /**
     * PATCH request
     *
     * @param string $endpoint
     * @param array $payload
     *
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws InvalidConfigurationException
     * @throws ResourceNotFoundException
     */
    public function patch(string $endpoint, array $payload = []): mixed
    {
        try {
            $response = $this->client->patch($endpoint, ['json' => $payload]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException | ServerException | RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();

            switch ($statusCode) {
                case 400:
                    throw new BadRequestException();
                case 403:
                    throw new AccessDeniedException();
                case 404:
                    throw new ResourceNotFoundException();
                case 422:
                    throw new InvalidConfigurationException();
                default:
                    $exceptionType = get_class($e);
                    $errorMessage = "$exceptionType: {$e->getMessage()}";
                    throw new RuntimeException($errorMessage, $e->getCode());
            }
        } catch (ConnectException $e) {
            throw new RuntimeException('Network error: ' . $e->getMessage(), $e->getCode());
        } catch (GuzzleException $e) {
            throw new RuntimeException('GuzzleException: ' . $e->getMessage(), $e->getCode());
        }
    }
}
