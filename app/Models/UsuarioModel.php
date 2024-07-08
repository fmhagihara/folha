<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['nome', 'login', 'senha', 'permissoes'];


    protected $dateFormat = 'datetime';

    protected $deletedField = 'deleted_at';


}