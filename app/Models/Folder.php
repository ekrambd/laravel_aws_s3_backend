<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bucket_id',
        'folder_name',
        'status',
    ];

    public function bucket()
    {
        return $this->belognsTo(Bucket::class);
    }

}
