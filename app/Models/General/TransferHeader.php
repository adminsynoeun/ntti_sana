<?php

namespace App\Models\General;

use App\Models\SystemSetup\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHeader extends Model
{
    use HasFactory;
    protected $table = 'transfer_header';
    protected $primaryKey = 'no';
    protected $keyType = 'string';
    protected $fillable = [
        '*',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function section()
    {
        return $this->belongsTo(Sections::class, 'sections_code', 'code');
    }

    public function skill()
    {
        return $this->belongsTo(Skills::class, 'skills_code', 'code');
    }

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teachers_code', 'code');
    }
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subjects_code', 'code');
    }
}
