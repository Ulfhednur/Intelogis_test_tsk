<?php
/**
 * @package Intelogis test task
 */

namespace Shipping;

/**
 * Class Shipping
 */
class SlowShipping extends AbstractShipping
{
    protected const API_URL = 'https://w1.tempadmin.pro/api/';
    private const BASE_PRICE = 150;

    protected string $RequestContentEncoding = self::REQUEST_CONTENT_TYPE_JSON;

    /**
     * @inheritDoc
     */
    public static function NormalizeReply(string $replyString): array|\stdClass
    {
        $result = json_decode($replyString);
        if(!is_object($result)) {
            $result = (object) [
                'price' => false,
                'date' => false,
                'error' => 'incorrect JSON',
            ];
        } elseif (!empty($result->error)) {
            $result->date = false;
            $result->price = false;
            unset($result->coefficient);
        } else {
            //дробных копеек не бывает. Округляем в свою пользу.
            $result->price = ceil($result->coefficient * self::BASE_PRICE * 100) / 100;
            unset($result->coefficient);
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function CalculateShippingPrice(string $from, string $to, float $weight, array $extData = []): array|\stdClass
    {
        $result = $this->makeRequest('slow_shipping', [
            'sourceKladr' => $from,
            'targetKladr' => $to,
            'weight' => $weight,
        ]);
        if(!empty($this->errors)) {
            $result->error .= implode("\r\n", $this->errors);
        }

        return $result;
    }

    /**
     * @inheritDoc
     * xml не поддерживается.
     */
    public static function EncodeXml(array $postFields): string
    {
        return '';
    }
}
