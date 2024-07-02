<?php

namespace App\Models;

use CodeIgniter\Model;

class VerbaModel extends Model
{
    protected $table = 'verba';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['codigo', 'id_grupo'];


    protected $dateFormat = 'datetime';

    protected $deletedField = 'deleted_at';


}