<?php

namespace Tsung\NovaLabelCreator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Tsung\NovaUserManagement\Traits\SaveToUpper;

class LabelType extends Model
{
    use SaveToUpper;

    protected $table = 'label_creator_label_types';

    protected $fillable = [
        'name',
        'attributes',
        'number_digits',
        'columns',
        'break_count',
        'design',
        'user_id',
    ];

    protected $no_upper = [
        'attributes',
        'design'
    ];

    protected $casts = [
        'attributes' => 'json'
    ];

    private static function validationError($message)
    {
        $messageBag = new MessageBag;

        $messageBag->add('error', __($message));

        throw ValidationException::withMessages($messageBag->getMessages());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($label_type) {

            if(! $label_type->user_id) {

                $label_type->user_id = auth()->user()->id;

            }

            $attributes = json_decode($label_type->attributes['attributes']);

            foreach($attributes as $key => $value) {

                if($value) {

                    $key = "[" . strtoupper($key) . "]";

                    if(strpos($label_type->design, $key) === false) {

                        self::validationError("Please add {$key} to label design");

                    }

                }

            }

            if(strpos($label_type->design,"[NUMBER]") === false) {
                self::validationError("Please add [NUMBER] to label design");
            }

        });


        static::updating(function($label_type) {

            $attributes = json_decode($label_type->attributes['attributes']);

            foreach($attributes as $key => $value) {

                if($value) {

                    $key = "[" . strtoupper($key) . "]";

                    if(strpos($label_type->design, $key) === false) {

                        self::validationError("Please add {$key} to label design");

                    }

                }

            }

            if(strpos($label_type->design,"[NUMBER]") === false) {

                self::validationError("Please add [NUMBER] to label design");

            }

        });
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
