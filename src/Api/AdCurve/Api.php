<?php
/**
 * API-information: http://implementation.adcurve.com/api/v1/
 */
namespace AuctioCore\Api\AdCurve;

use AuctioCore\Api\AdCurve\Entity\Product;
use GuzzleHttp\Client;

class Api
{

    private Client $client;
    private array $clientHeaders;
    private string $shopId;
    private array $messages;
    private array $errorData;

    /**
     * Constructor
     *
     * @param string $hostname
     * @param string $apiKey
     * @param string $shopId
     * @param boolean $debug
     */
    public function __construct(string $hostname, string $apiKey, string $shopId, $debug = false)
    {
        // Set client
        $this->client = new Client(['base_uri'=>$hostname, 'http_errors'=>false, 'debug'=>$debug]);

        // Set default header for client-requests
        $this->clientHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Api-Key' => $apiKey,
        ];

        // Set shop-id
        $this->shopId = $shopId;

        // Set error-messages
        $this->messages = [];
        $this->errorData = [];
    }

    /**
     * Set error-data
     *
     * @param array|string $data
     */
    public function setErrorData($data)
    {
        if (!is_array($data)) $data = [$data];
        $this->errorData = $data;
    }

    /**
     * Get error-data
     *
     * @return array
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }

    /**
     * Set error-message
     *
     * @param array|string $messages
     */
    public function setMessages($messages)
    {
        if (!is_array($messages)) $messages = [$messages];
        $this->messages = $messages;
    }

    /**
     * Add error-message
     *
     * @param array|string $message
     */
    public function addMessage($message)
    {
        if (!is_array($message)) $message = [$message];
        $this->messages = array_merge($this->messages, $message);
    }

    /**
     * Get error-messages
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param object|array $products
     * @return bool|array
     */
    public function createProducts($products)
    {
        // Check input
        if (is_array($products) && count($products) > 0) {
            foreach ($products AS $k => $product) {
                if (!($product instanceof Product)) {
                    $this->setMessages("No valid input");
                    return false;
                } else {
                    $products[$k] = json_decode($product->encode(false));
                }
            }
        } else {
            if (!($products instanceof Product)) {
                $this->setMessages("No valid input");
                return false;
            } else {
                $products = [json_decode($products->encode(false))];
            }
        }

        // Unset array-index
        $products = array_values($products);

        // Prepare request
        $requestHeader = $this->clientHeaders;

        // Execute request
        $result = $this->client->request('POST', 'v1/shops/' . $this->shopId . '/shop_products/batch', ["headers"=>$requestHeader, "body"=>json_encode($products)]);
        if ($result->getStatusCode() == 200 || $result->getStatusCode() == 204) {
            $response = json_decode((string) $result->getBody());

            // Return
            if (!isset($response->errors)) {
                return $response;
            } else {
                $this->setErrorData($response);
                $this->setMessages($response->errors);
                return false;
            }
        } else {
            $response = json_decode((string) $result->getBody());
            $this->setErrorData($response);
            $this->setMessages($result->getStatusCode() . ": " . $result->getReasonPhrase());
            return false;
        }
    }

    public function getProduct($variantId)
    {
        // Prepare request
        $requestHeader = $this->clientHeaders;

        // Execute request
        $result = $this->client->request('GET', 'v2/shops/' . $this->shopId . '/shop_products/' . $variantId, ["headers"=>$requestHeader]);
        if ($result->getStatusCode() == 200) {
            $response = json_decode((string) $result->getBody());

            // Return
            if (!isset($response->errors)) {
                return $response;
            } else {
                $this->setErrorData($response);
                $this->setMessages($response->errors);
                return false;
            }
        } else {
            $response = json_decode((string) $result->getBody());
            $this->setErrorData($response);
            $this->setMessages($result->getStatusCode() . ": " . $result->getReasonPhrase());
            return false;
        }
    }

}