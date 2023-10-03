<?php
/**
 * @package Intelogis test task
 */

namespace Shipping;

/**
 * Class Shipping
 */
abstract class AbstractShipping
{
    protected const API_URL = '';

    public const REQUEST_CONTENT_TYPE_RAW = 'raw';
    public const REQUEST_CONTENT_TYPE_JSON = 'json';
    public const REQUEST_CONTENT_TYPE_XML = 'xml';

    protected string $RequestContentEncoding;
    protected array $errors = [];

    /**
     * @param string $method
     * @param array|string $postFields
     * @param bool $post
     * @return array|stdClass
     */
    protected function makeRequest(string $method, array|string $postFields, bool $post = true): array|\stdClass
    {
        $url = static::API_URL . $method;

        if (!$post) {
            if(is_string($postFields)) {
                $url .= '?' . $postFields;
            } else {
                $url .= '?' . http_build_query($postFields);
            }
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($postFields) ? $postFields : match($this->RequestContentEncoding){
                self::REQUEST_CONTENT_TYPE_RAW => http_build_query($postFields),
                self::REQUEST_CONTENT_TYPE_JSON => json_encode($postFields),
                self::REQUEST_CONTENT_TYPE_XML => static::EncodeXml($postFields),
            });
        }

        $result = curl_exec($ch);
        if ($errors = curl_error($ch)) {
            $this->errors[] = $errors;
        }
        curl_close($ch);

        return static::NormalizeReply($result);
    }

    /**
     * Поддержка xml для легкй расширяемости в контексте транспортных компаний.
     * Если не требуется, еспользовать return '';
     * @param array $postFields
     * @return string
     */
    abstract protected static function EncodeXml(array $postFields): string;

    /**
     * Нормализация ответа в заданный вид
     *
     * В обоих реализуемых классах часть кода метода совпадает, это вызвано тем, что оба реализуемых API возвращают
     * JSON, и, при том, с похожей структурой. Но, чисто теоретически, API других транспортных компаний могут возвращать
     * и XML, и CSV и какие нибудь свои, не стандартизированные форматы. Таким образом, при условии легкй расширяемости
     * в контексте транспортных компаний нет смысла пытаться унифицировать обработку ответа и описывать её в
     * родительском классе. Можно понаделать абстрактных классов-прослоек на каждый content-type, но это выглядит
     * избыточным, если транспортных компаний меньше, хотя бы, тридцати. В России столько есть, вообще?
     *
     * @param string $replyString
     * @return array|stdClass
     */
    abstract protected static function NormalizeReply(string $replyString): array|\stdClass;

    /**
     * Вызов калькулятора
     * @param string $from
     * @param string $to
     * @param float $weight
     * @return array|stdClass
     */
    abstract public function CalculateShippingPrice(string $from, string $to, float $weight, array $extData = []): array|\stdClass;
}
