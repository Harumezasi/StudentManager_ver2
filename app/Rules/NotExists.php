<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NotExists implements Rule
{
    private $tableName, $columnName;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($argTableName, $argColumnName)
    {
        //
        $this->tableName = $argTableName;
        $this->columnName = $argColumnName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        return !DB::table($this->tableName)->where($this->columnName, $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.not_exists');
    }
}
