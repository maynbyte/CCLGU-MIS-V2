<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FinancialAssistance extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'financial_assistances';

    protected $appends = [
        'requirements',
    ];

    protected $dates = [
        'date_interviewed',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'directory_id',
        'family_composition',
        'user',
        'problem_presented',
        'date_interviewed',
        'assessment',
        'recommendation',
        'amount',
        'scheduled_fa',
        'status',
        'date_claimed',
        'note',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getDateInterviewedAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setDateInterviewedAttribute($value)
    {
        $this->attributes['date_interviewed'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getRequirementsAttribute()
    {
        return $this->getMedia('requirements');
    }

    public function directory()
    {
        return $this->belongsTo(\App\Models\Directory::class, 'directory_id');
    }
}
