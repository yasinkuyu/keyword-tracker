<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KeywordPosition extends Model
{
    use HasFactory;

    public $timestamps = true;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = ['position', 'serice_id', 'domain_id', 'keyword_id', 'country', 'language'];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class, 'keyword_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    // Pozisyonu güncelle veya oluştur
    public static function updateOrCreatePosition($keyword_id, $position, $domain_id, $country, $language)
    {
        $queryDate = Carbon::now()->toDateString();

        $existingPosition = self::where('keyword_id', $keyword_id)
            ->where('domain_id', $domain_id)
            ->where('country', $country)
            ->where('language', $language)
            ->whereRaw('DATE(created_at) = ?', [$queryDate])
            ->first();

        if ($existingPosition) {
            $existingPosition->position = $position;
            $existingPosition->save();
        } else {
            self::create([
                'keyword_id' => $keyword_id,
                'position' => $position,
                'domain_id' => $domain_id,
                'country' => $country,
                'language' => $language,
                'created_at' => $queryDate,
                'updated_at' => Carbon::now(),
            ]);
        }

    }
 
}
