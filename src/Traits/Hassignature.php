<?php
namespace Pdik\laravel_signhost\Traits;
trait Hassignature
{

    public static function bootHassignature()
    {
       static::created(function (Model $taggableModel) {
                if (count($taggableModel->queuedTags) === 0) {
                    return;
                }

                $taggableModel->attachTags($taggableModel->queuedTags);

                $taggableModel->queuedTags = [];
            });

            static::deleted(function (Model $deletedModel) {
                $tags = $deletedModel->tags()->get();

                $deletedModel->detachTags($tags);
            });
    }

}