<?php

namespace Omnipay\Mollie\Message\Request;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\ItemInterface;
use Omnipay\Mollie\Item;

/**
 * This class holds all the common things for all of Mollie requests.
 *
 * @see https://docs.mollie.com/index
 */
abstract class AbstractMollieRequest extends AbstractRequest
{
    const POST = 'POST';
    const GET = 'GET';
    const DELETE = 'DELETE';

    /**
     * @var string
     */
    protected $apiVersion = "v2";

    /**
     * @var string
     */
    protected $baseUrl = 'https://api.mollie.com/';

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Set the items in this order
     *
     * @param Item[] $items An array of items in this order
     * @return AbstractRequest
     */
    public function setItems($items)
    {
        $orderItems = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $orderItems[] = new Item($item);
            } elseif ($item instanceof ItemInterface) {
                $orderItems[] = $item;
            } else {
                throw new \InvalidArgumentException(
                    'Item should be an associative array or implement '.ItemInterface::class
                );
            }
        }

        return parent::setItems($orderItems);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function sendRequest($method, $endpoint, array $data = null)
    {
        $response = $this->httpClient->request(
            $method,
            $this->baseUrl . $this->apiVersion . $endpoint,
            [
                'Authorization' => 'Bearer ' . $this->getApiKey(),
                'Content-Type' => 'application/json',
            ],
            ($data === null || $data === []) ? null : json_encode($data)
        );

        return json_decode($response->getBody(), true);
    }


    protected function createAmountObject($amount)
    {
        return isset($amount) ? [
            'currency' => $this->getCurrency(),
            'value' => $this->formatCurrency($amount),
        ] : null;
    }
}
