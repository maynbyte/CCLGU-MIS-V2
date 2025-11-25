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


class Directory extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'directories';

    protected $appends = [
        'profile_picture',
    ];

    public const GENDER_SELECT = [
        'Male'   => 'Male',
        'Female' => 'Female',
    ];

    protected $dates = [
        'birthday',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const LIFE_STATUS_SELECT = [
        'Alive'    => 'Alive',
        'Deceased' => 'Deceased',
    ];

    public const COMELEC_STATUS_SELECT = [
        'Registered'   => 'Registered',
        'Unregistered' => 'Unregistered',
    ];

    public const CIVIL_STATUS_SELECT = [
        'Single'    => 'Single',
        'Married'   => 'Married',
        'Live-in'   => 'Live-in',
        'Separated' => 'Separated',
        'Divorced'  => 'Divorced',
        'Widowed'   => 'Widowed',
    ];

    protected $fillable = [
        'last_name',
        'maiden_surname',
        'first_name',
        'middle_name',
        'suffix',
        'email',
        'contact_no',
        'birthday',
        'place_of_birth',
        'nationality',
        'gender',
        'highest_edu',
        'civil_status',
        'religion',
        'street_no',
        'street',
        'city',
        'occupation',
        'province',
        'barangay_id',
        'comelec_status',
        'life_status',
        'description',
        'remarks',
        'created_at',
        'updated_at',
        'deleted_at',
        'barangay_id',
        'barangay_other',
    ];

    public const HIGHEST_EDU_SELECT = [
        'No Grade Completed'        => 'No Grade Completed',
        'Early childhood Educ.'     => 'Early childhood Educ.',
        'Elementary Undergraduate'  => 'Elementary Undergraduate',
        'Elementary Graduate'       => 'Elementary Graduate',
        'High School Undergraduate' => 'High School Undergraduate',
        'High School Graduate'      => 'High School Graduate',
        'Vocational'                => 'Vocational',
        'College Undergraduate'     => 'College Undergraduate',
        'College Graduate'          => 'College Graduate',
        'Master\'s Degree'          => 'Master\'s Degree',
        'Master\'s Graduate'        => 'Master\'s Graduate',
        'Doctoral Degree'           => 'Doctoral Degree',
        'Doctoral Graduate'         => 'Doctoral Graduate',
    ];

    public const RELIGION_SELECT = [
        'Roman Catholic'                             => 'Roman Catholic',
        'Iglesia ni Cristo'                          => 'Iglesia ni Cristo',
        'Muslim'                                     => 'Muslim',
        'Born Again'                                 => 'Born Again',
        'Jehovah\'s Witnesses'                       => 'Jehovah\'s Witnesses',
        'Philippine Independent Church'              => 'Philippine Independent Church',
        'Seventh-day Adventist'                      => 'Seventh-day Adventist',
        'Bible Baptist Church'                       => 'Bible Baptist Church',
        'United Church of Christ in the Philippines' => 'United Church of Christ in the Philippines',
        'Church of Christ'                           => 'Church of Christ',
        'Protestant'                                 => 'Protestant',
        'Islam'                                      => 'Islam',
        'Mormons'                                    => 'Mormons',
        'Buddhist'                                   => 'Buddhist',
        'Others'                                     => 'Others',
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

    public function getProfilePictureAttribute()
    {
        $file = $this->getMedia('profile_picture')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getBirthdayAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function financialAssistances()
    {
        return $this->hasMany(\App\Models\FinancialAssistance::class, 'directory_id');
    }

    public function latestFinancialAssistance()
    {
        return $this->hasOne(\App\Models\FinancialAssistance::class, 'directory_id')->latestOfMany();
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function ngos()
    {
        return $this->belongsToMany(Ngo::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(SectorGroup::class);
    }

    public function familycompositions()
    {
        return $this->hasMany(Familycomposition::class, 'directory_id');
    }

    public function setBarangayOtherAttribute($value): void
    {
        // if a valid barangay_id exists, ignore custom text
        if (!empty($this->attributes['barangay_id'])) {
            $this->attributes['barangay_other'] = null;
        } else {
            $this->attributes['barangay_other'] = $value;
        }
    }

    


}
