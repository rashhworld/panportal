<?php 
namespace App\Models;
use CodeIgniter\Model;

class PanAdmin_Model extends Model
{
    protected $table = 'panadmin';
    protected $primaryKey = 'aId';
    protected $allowedFields = ['aEmail', 'aPass'];
}