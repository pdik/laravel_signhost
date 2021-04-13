<?php

namespace Pdik\laravel_signhost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class signatures extends Model
{
    protected $table = 'signhost-signatures';
    
}