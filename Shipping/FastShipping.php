<?php
/**
 * @package Intelogis test task
 */

namespace Shipping;

/**
 * Class Shipping
 */
class FastShipping extends AbstractShipping
{
    protected const API_URL = 'https://w1.tempadmin.pro/api/';

    protected string $RequestContentEncoding = self::REQUEST_CONTENT_TYPE_RAW;

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
            unset($result->period);
        } else {
            if (date('H') >= 18) {
                $start = 'tomorrow';
            } else {
                $start = 'today';
            }

            $result->date = date('Y-m-d', strtotime($start . ' + ' . $result->period . ' day'));
            unset($result->period);
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function CalculateShippingPrice(string $from, string $to, float $weight, array $extData = []): array|\stdClass
    {
        $result = $this->makeRequest('fast_shipping', [
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
