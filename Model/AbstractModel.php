<?php
namespace Model;

abstract class AbstractModel
{
    public function load($data)
    {
        foreach($data as $key => $value) {
            if(property_exists ($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}