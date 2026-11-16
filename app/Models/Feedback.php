<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    // Laravel menebak nama tabel 'feedback' (tanpa s), padahal migration membuat 'feedbacks'.
    protected $table = 'feedbacks';

    protected $fillable = [
        'student_id', 'name', 'class_info', 'category', 'message',
        'rating', 'status', 'balasan', 'is_unfreeze_request', 'read_at',
    ];

    protected function casts(): array
    {
        return ['is_unfreeze_request' => 'boolean', 'read_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
