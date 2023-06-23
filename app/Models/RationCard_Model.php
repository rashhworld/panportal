<?php

namespace App\Models;

use CodeIgniter\Model;

class RationCard_Model extends Model
{
    protected $table = 'rationdata';
    protected $primaryKey = 'raId';
    protected $allowedFields = ['raName', 'raState', 'raNumber'];
}
