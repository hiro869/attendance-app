<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceCorrectionRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i'],
            'break_start'  => ['nullable', 'date_format:H:i'],
            'break_end'    => ['nullable', 'date_format:H:i'],
            'note'         => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator){

            $start = $this->start_time;
            $end   = $this->end_time;
            $bs    = $this->break_start;
            $be    = $this->break_end;

            // ===============================
            // 出勤・退勤 チェック
            // ===============================
            if ($start && $end)
            {
                $s = Carbon::createFromFormat('H:i', $start);
                $e = Carbon::createFromFormat('H:i', $end);

                if ($s->gte($e)) {
                    $validator->errors()->add(
                        'start_time',
                        '出勤時間もしくは退勤時間が不適切な値です'
                    );
                }
            }

            // ===============================
            // 休憩開始チェック
            // ===============================
            if ($bs)
            {
                $s = Carbon::createFromFormat('H:i', $start);
                $e = Carbon::createFromFormat('H:i', $end);
                $b = Carbon::createFromFormat('H:i', $bs);

                // 出勤より前・退勤より後 → NG
                if ($b->lte($s) || $b->gte($e)) {
                    $validator->errors()->add(
                        'break_start',
                        '休憩時間が不適切な値です'
                    );
                }
            }

            // ===============================
            // 休憩終了チェック
            // ===============================
            if ($be)
            {
                $s       = Carbon::createFromFormat('H:i', $start);
                $e       = Carbon::createFromFormat('H:i', $end);
                $b_end   = Carbon::createFromFormat('H:i', $be);
                $b_start = $bs ? Carbon::createFromFormat('H:i', $bs) : null;

                // 退勤より後 → NG
                if ($b_end->gte($e)) {
                    $validator->errors()->add(
                        'break_end',
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }

                // ★休憩終了 ≤ 休憩開始 → NG
                if ($b_start && $b_end->lte($b_start)) {
                    $validator->errors()->add(
                        'break_end',
                        '休憩時間が不適切な値です'
                    );
                }

                // ★休憩終了 ≤ 出勤 → NG
                if ($b_end->lte($s)) {
                    $validator->errors()->add(
                        'break_end',
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'start_time.required'      => '出勤時間は必須です',
            'end_time.required'        => '退勤時間は必須です',
            'note.required'            => '備考を記入してください',
            'start_time.date_format'   => '時刻形式で入力してください',
            'end_time.date_format'     => '時刻形式で入力してください',
            'break_start.date_format'  => '時刻形式で入力してください',
            'break_end.date_format'    => '時刻形式で入力してください',
        ];
    }
}
