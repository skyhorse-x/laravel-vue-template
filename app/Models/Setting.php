<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'type', 'group', 'title', 'description', 'sort'])]
class Setting extends Model
{
    use HasFactory;

    protected $table = 'luma_settings';

    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'number':
                return (int) $this->value;
            case 'boolean':
                return $this->value === 'true' || $this->value === '1';
            case 'array':
                return json_decode($this->value, true) ?? [];
            case 'password':
                return $this->value;
            default:
                return $this->value;
        }
    }

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    public static function setValue($key, $value, $type = 'string')
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            $setting = new self(['key' => $key, 'type' => $type]);
        }
        if (is_array($value)) {
            $setting->value = json_encode($value);
            $setting->type = 'array';
        } elseif (is_bool($value)) {
            $setting->value = $value ? 'true' : 'false';
            $setting->type = 'boolean';
        } else {
            $setting->value = (string) $value;
            if ($setting->exists) {
                // 已存在的记录保持原类型
            } else {
                $setting->type = $type;
            }
        }
        $setting->save();
        return $setting;
    }
}
