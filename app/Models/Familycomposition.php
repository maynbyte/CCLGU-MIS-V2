<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Familycomposition extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'familycompositions';

    protected $dates = [
        'family_birthday',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const FAMILY_CIVIL_STATUS_SELECT = [
        'Single'    => 'Single',
        'Married'   => 'Married',
        'Divorced'  => 'Divorced',
        'Widowed'   => 'Widowed',
        'Separated' => 'Separated',
        'Others'    => 'Others',
    ];

    protected $fillable = [
        'family_name',
        'family_birthday',
        'family_relationship',
        'family_civil_status',
        'family_highest_edu',
        'occupation',
        'remarks',
        'others',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const FAMILY_RELATIONSHIP_SELECT = [
        'Father'      => 'Father',
        'Mother'      => 'Mother',
        'Brother'     => 'Brother',
        'Sister'      => 'Sister',
        'Son'         => 'Son',
        'Daughter'    => 'Daughter',
        'Grandfather' => 'Grandfather',
        'Grandmother' => 'Grandmother',
        'Uncle'       => 'Uncle',
        'Aunt'        => 'Aunt',
        'Cousin'      => 'Cousin',
        'Guardian'    => 'Guardian',
        'Other'       => 'Other',
    ];

    public const FAMILY_HIGHEST_EDU_SELECT = [
        'No Grade Completed'        => 'No Grade Completed',
        'Early childhood Educ.'     => 'Early childhood Educ.',
        'Elementary Undergraduate'  => 'Elementary Undergraduate',
        'Elementary Graduate'       => 'Elementary Graduate',
        'High School Undergraduate' => 'High School Undergraduate',
        'High School Graduate'      => 'High School Graduate',
        'Vocational'                => 'Vocational',
        'College Undergraduate'     => 'College Undergraduate',
        'College Graduate'          => 'College Graduate',
        'Masters Degree'            => 'Masters Degree',
        'Masters Graduate'          => 'Masters Graduate',
        'Doctoral Degree'           => 'Doctoral Degree',
        'Doctoral Graduate'         => 'Doctoral Graduate',
        'Others'                    => 'Others',
    ];

    // app/Models/Familycomposition.php
    protected $casts = [
        'family_name'         => 'array',
        'family_birthday'     => 'date',
        'family_relationship' => 'array',
        'family_civil_status' => 'array',
        'family_highest_edu'  => 'array',
        'occupation'          => 'array',
        'remarks'             => 'array',
        'others'              => 'array',
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

  
   public function directory()
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }




}
