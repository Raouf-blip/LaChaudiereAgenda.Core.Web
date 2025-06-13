<?php

namespace Chaudiere\core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model {

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $keyType = 'int';
    protected $fillable = ['name', 'description'];

    public function events() {
        return $this->hasMany(Events::class, 'category_id', 'id');
    }
}
