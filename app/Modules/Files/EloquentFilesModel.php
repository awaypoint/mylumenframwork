<?php

namespace App\Modules\Files;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentFilesModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'files';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'module_type', 'company_id', 'relation_field', 'file_name', 'preview_url', 'url', 'oss_key', 'remark', 'extra_fields'];
}
