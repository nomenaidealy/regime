<?php

namespace App\Models;

use CodeIgniter\Model;

class CodePromoModel extends Model
{
    protected $table = 'code_promo';

    public function getValidCode($code)
    {
        return $this->where('code', $code)
            ->where('id_user_utilise', null)
            ->first();
    }

    public function markUsed($id, $userId)
    {
        return $this->update($id, [
            'id_user_utilise'  => $userId,
            'date_utilisation' => date('Y-m-d H:i:s')
        ]);
    }
}