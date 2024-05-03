<?php

namespace App\Models;

use CodeIgniter\Model;

class EncargoModel extends Model
{
    protected $table = 'encargos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['competencia', 'inss', 'fgts', 'pis'];


    protected $dateFormat = 'datetime';

    protected $deletedField = 'deleted_at';


}