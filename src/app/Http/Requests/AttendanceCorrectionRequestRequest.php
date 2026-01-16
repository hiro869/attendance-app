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
        'start_time' => ['required', 'date_format:H:i'],
        'end_time'   => ['required', 'date_format:H:i'],
        'note'       => ['required'],

        // ★ 休憩（配列）
        'breaks.*.start' => ['nullable', 'date_format:H:i'],
        'breaks.*.end'   => ['nullable', 'date_format:H:i'],
    ];
}

public function withValidator($validator)
{
    $validator->after(function ($validator) {

        $start = $this->start_time;
        $end   = $this->end_time;

        if (!$start || !$end) return;

        $workStart = Carbon::createFromFormat('H:i', $start);
        $workEnd   = Carbon::createFromFormat('H:i', $end);

        // ① 出勤・退勤
        if ($workStart->gte($workEnd)) {
            $validator->errors()->add(
                'start_time',
                '出勤時間もしくは退勤時間が不適切な値です'
            );
        }

        // ②③ 休憩チェック（配列）
        foreach ($this->breaks ?? [] as $i => $b) {

            if (empty($b['start']) || empty($b['end'])) {
                continue;
            }

            $bs = Carbon::createFromFormat('H:i', $b['start']);
            $be = Carbon::createFromFormat('H:i', $b['end']);

            // 休憩開始が出勤前 or 退勤後
            if ($bs->lte($workStart) || $bs->gte($workEnd)) {
                $validator->errors()->add(
                    "breaks.$i.start",
                    '休憩時間が不適切な値です'
                );
            }

            // 休憩終了が退勤後
            if ($be->gte($workEnd)) {
                $validator->errors()->add(
                    "breaks.$i.end",
                    '休憩時間もしくは退勤時間が不適切な値です'
                );
            }

            // 休憩終了 ≤ 開始
            if ($be->lte($bs)) {
                $validator->errors()->add(
                    "breaks.$i.end",
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
