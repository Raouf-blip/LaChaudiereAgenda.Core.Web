<?php

namespace Chaudiere\entities;

use Illuminate\Database\Eloquent\Model;
use Chaudiere\entities\Categories;

class Events extends Models {

    protected $table = 'events';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $keyType = 'string';

    public function categories() {
        return $this->hasMany(Categories::class, 'id', 'title');
    }
}