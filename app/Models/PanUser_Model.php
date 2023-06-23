<?php 
namespace App\Models;
use CodeIgniter\Model;

class PanUser_Model extends Model
{
    protected $table = 'panuser';
    protected $primaryKey = 'uId';
    protected $allowedFields = ['uName', 'uPhone', 'uEmail', 'uPass', 'uToken', 'uWallet', 'uReferralCode', 'uReferedBy'];
}