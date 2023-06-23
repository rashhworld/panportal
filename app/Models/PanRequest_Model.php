<?php 
namespace App\Models;
use CodeIgniter\Model;

class PanRequest_Model extends Model
{
    protected $table = 'panrequest';
    protected $primaryKey = 'pId';
    protected $allowedFields = ['uId', 'pName', 'pAdhar', 'pStatus', 'pPAN'];
}