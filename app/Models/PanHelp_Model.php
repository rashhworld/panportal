<?php 
namespace App\Models;
use CodeIgniter\Model;

class PanHelp_Model extends Model
{
    protected $table = 'panhelp';
    protected $primaryKey = 'hId';
    protected $allowedFields = ['uId', 'hName', 'hMsg', 'hStatus'];
}