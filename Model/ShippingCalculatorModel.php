<?php
namespace Model;

class ShippingCalculatorModel extends AbstractModel
{
    protected array $availableShippings = [
        'Shipping\FastShipping' => 'Быстрая доставка',
        'Shipping\SlowShipping' => 'Медленная доставка',
    ];
    protected string $from;
    protected string $to;
    protected float $weight;
    protected array $extData = [];

    public function calculate(): array
    {
        $result = [];
        foreach($this->availableShippings as $shippingClass => $shippingTitle) {
            $res = (new $shippingClass())->CalculateShippingPrice($this->from, $this->to, $this->weight, $this->extData);
            $res->shippingId = $shippingClass;
            $res->shippingTitle = $shippingTitle;
            $result[] = $res;
        }
        return $result;
    }
}