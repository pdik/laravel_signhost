<?php
namespace pdik\signhost\Traits;
trait Hassignature
{

    public static function bootHassignature()
    {
       static::created(function (Model $model) {

       });
    }

}