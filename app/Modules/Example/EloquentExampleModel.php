<?php

namespace App\Modules\_bigname_;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eloquent_bigname_Model extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = '_smallname_';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = [];
}
