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
use Illuminate\Support\Facades\DB;

class FinancialAssistance extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'financial_assistances';

    /*
    |--------------------------------------------------------------------------
    | Select/Option Lists
    |--------------------------------------------------------------------------
    */
    public const TYPE_OF_ASSISTANCE = [
        'Financial Assistance',
        'Guarantee Letter',
        'Burial Assistance',
        'Medical Assistance',
        'Education Assistance',
        'Solicitation',
    ];

    public const STATUS_OPTIONS = [
        'Ongoing',    // newly added default status
        'Pending',
        'Claimed',
        'Cancelled',
    ];

    public const PROBLEM_PRESENTED_OPTIONS = [
        'Medical Assistance',
        'Burial Assistance',
        'Food Assistance',
        'Shelter Assistance',
        'Educational Assistance',
        'Transportation Assistance',
        'Others',
    ];

    public const SWO_NAMES = [
        'Cristine Joy G. Mingo, RSW',
        'Rowena C. Andrade, RSW',
    ];

    public const SWO_DESIGS = [
        'Social Welfare Officer I',
        'Social Welfare Officer IV',
        'Social Welfare Officer III',
        'Social Welfare Officer II',
        "City Social Welfare & Dev't Officer",
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors / Appends / Dates
    |--------------------------------------------------------------------------
    */
    protected $appends = [
        'requirements',
    ];

    protected $dates = [
        'date_interviewed',
        'date_claimed',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'directory_id',
        // NEW fields
        'type_of_assistance',
        'patient_name',
        'claimant_is_patient',
        'requirement_checklist',   // JSON array
        'status',
        'problem_presented_value', // JSON array (selected problems)
        'social_welfare_name',
        'social_welfare_desig',
        // Existing fields
        'family_composition',
        'user',
        'problem_presented',
        'date_interviewed',
        'assessment',
        'recommendation',
        'amount',
        'scheduled_fa',
        'date_claimed',
        'note',
        'created_at',
        'updated_at',
        'deleted_at',
        'amount',
        'reference_no',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'requirement_checklist'   => 'array',
        'problem_presented_value' => 'array',
        'claimant_is_patient'     => 'boolean',
        'date_interviewed'        => 'date',  // if column type is DATE
        'date_claimed'            => 'datetime',  // if column type is DATE
    ];

    public function setAmountAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['amount'] = null;
            return;
        }

        // Remove any non-digit/decimal characters (e.g., "₱1,000.50" -> "1000.50")
        $clean = preg_replace('/[^\d.]/', '', (string) $value);

        // If still not numeric, store NULL to avoid casting issues
        if (!is_numeric($clean)) {
            $this->attributes['amount'] = null;
            return;
        }

        // Store a normalized string with 2 decimals
        $this->attributes['amount'] = number_format((float) $clean, 2, '.', '');
    }

    public function getAmountFormattedAttribute()
    {
        $raw = $this->attributes['amount'] ?? null;
        if ($raw === null || $raw === '') return null;
        return number_format((float) $raw, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Serialization
    |--------------------------------------------------------------------------
    */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getRequirementsAttribute()
    {
        return $this->getMedia('requirements');
    }

    /*
    |--------------------------------------------------------------------------
    | Date Mutators
    |--------------------------------------------------------------------------
    */
    public function getDateInterviewedAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value)
            ->format(config('panel.date_format') . ' ' . config('panel.time_format'))
            : null;
    }

    public function setDateInterviewedAttribute($value)
    {
        $this->attributes['date_interviewed'] = $value
            ? Carbon::createFromFormat(
                config('panel.date_format') . ' ' . config('panel.time_format'),
                $value
            )->format('Y-m-d H:i:s')
            : null;
    }

    public function getDateClaimedAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format'))
            : null;
    }


    public function setDateClaimedAttribute($value)
    {
        $this->attributes['date_claimed'] = $this->normalizeDateTimeToSql($value);
    }

    /**
     * Accepts multiple input formats (including <input type="datetime-local">)
     * and normalizes to 'Y-m-d H:i:s' or null.
     */
    protected function normalizeDateTimeToSql($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        // Try common formats your UI might send
        $formats = [
            'Y-m-d\TH:i',        // <input type="datetime-local"> without seconds
            'Y-m-d\TH:i:s',      // datetime-local with seconds
            'Y-m-d H:i',         // space, no seconds
            'Y-m-d H:i:s',       // space with seconds
            // Your panel config (leave last; often expects seconds):
            config('panel.date_format') . ' ' . config('panel.time_format'),
        ];

        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                // try next
            }
        }

        // Final fallback
        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null; // or keep the raw value if you prefer
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function directory()
    {
        return $this->belongsTo(\App\Models\Directory::class, 'directory_id');
    }

    // Add this relation
    public function addedBy()
    {
        // foreign key column is literally "user"
        return $this->belongsTo(\App\Models\User::class, 'user');
    }

     // OPTIONAL: auto-generate if empty on create
    protected static function booted(): void
    {
        static::creating(function (self $fa) {
            if (empty($fa->reference_no)) {
                $fa->reference_no = self::nextReferenceNo();
            }
        });
    }

    // Yearly-resetting sequence: CC-FA-YYYY-000001
    public static function nextReferenceNo(): string
    {
        return DB::transaction(function () {
            $scope = 'financial_assistances';
            $year  = now()->year;

            // sequences table holds counters per (scope,year)
            $row = DB::table('sequences')
                ->where('scope', $scope)
                ->where('year',  $year)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                DB::table('sequences')->insert([
                    'scope'        => $scope,
                    'year'         => $year,
                    'next_number'  => 2, // we will return 1 below
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $next = 1;
            } else {
                $next = (int) $row->next_number;
                DB::table('sequences')
                    ->where('scope', $scope)
                    ->where('year',  $year)
                    ->update([
                        'next_number' => $next + 1,
                        'updated_at'  => now(),
                    ]);
            }

            return sprintf('CC-FA-%d-%06d', $year, $next);
        });
    }

    
}
