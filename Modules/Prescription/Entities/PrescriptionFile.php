<?php

namespace Modules\Prescription\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PrescriptionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}