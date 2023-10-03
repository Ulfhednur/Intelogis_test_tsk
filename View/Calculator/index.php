<?php

use Lib\Application;

$app = Application::getInstance();
?>
<h1><?= $app->title ?></h1>
<section id="calc-form-wrapper">
    <form action="/calculator/calculate" id="calc-form">
        <div class="mb-3">
            <label for="inp_from" class="form-label">From</label>
            <input id="inp_from" type="text" class="form-control" name="from">
        </div>
        <div class="mb-3">
            <label for="inp_to" class="form-label">To</label>
            <input id="inp_to" type="text" class="form-control" name="to">
        </div>
        <div class="mb-3">
            <label for="inp_weight" class="form-label">Weight</label>
            <input id="inp_weight" type="text" class="form-control" name="weight">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>
<section id="answer-section"></section>
