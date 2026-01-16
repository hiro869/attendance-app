<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
{
    return [
        'start_time'  => ['required', 'date_format:H:i'],
        'end_time'    => ['required', 'date_format:H:i'],
        'break_start' => ['nullable', 'date_format:H:i'],
        'break_end'   => ['nullable', 'date_format:H:i'],
        'note'        => ['required'],
    ];
}

    public function messages(): array
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $start = Carbon::createFromFormat('H:i', $this->start_time);
            $end   = Carbon::createFromFormat('H:i', $this->end_time);

            // FN039-① 出勤 > 退勤
            if ($start->gte($end)) {
                $validator->errors()->add(
                    'end_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // FN039-② 休憩開始
            if ($this->break_start) {
                $breakStart = Carbon::createFromFormat('H:i', $this->break_start);

                if ($breakStart->lt($start) || $breakStart->gt($end)) {
                    $validator->errors()->add(
                        'break_start',
                        '休憩時間が不適切な値です'
                    );
                }
            }

            // FN039-③ 休憩終了
            if ($this->break_end) {
                $breakEnd = Carbon::createFromFormat('H:i', $this->break_end);

                if ($breakEnd->gt($end)) {
                    $validator->errors()->add(
                        'break_end',
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
