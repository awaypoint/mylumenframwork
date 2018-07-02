<?php

namespace App\Modules\Book;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentBookModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'website_book';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'expert_id', 'status', 'company_name', 'mobile', 'address', 'detail', 'created_at'];

    public $guardFillable = ['id'];
}
