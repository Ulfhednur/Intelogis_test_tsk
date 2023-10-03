<?php

namespace Controller;

use Model\ShippingCalculatorModel;

class CalculatorController extends AbstractController
{
    public function Index(): string
    {
        $this->app->title = 'Shipping Calculator';
        return $this->render('index');
    }

    public function Calculate(): string
    {
        $model = new ShippingCalculatorModel();
        $model->load([
            'weight' => $this->getInput()->getFloat('weight'),
            'from' => $this->getInput()->getString('from'),
            'to' => $this->getInput()->getString('to'),
            'extData' => $this->getInput()->getArray('extData'),
        ]);
        return $this->asJson($model->calculate());
    }

    public function selectShipping(): string
    {

    }
}