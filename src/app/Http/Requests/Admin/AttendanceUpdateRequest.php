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
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'note'        => ['required'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i', 'after:breaks.*.start'],
        ];
    }

    public function messages(): array
    {
        return [
            // ここで必要なエラーメッセージだけを表示
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',  // 出勤時間が退勤時間より後の場合
            'breaks.*.start.before' => '休憩時間が不適切な値です',  // 休憩開始時間が不適切
            'breaks.*.end.after' => '休憩時間もしくは退勤時間が不適切な値です', // 休憩終了時間が不適切
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = Carbon::createFromFormat('H:i', $this->start_time);
            $end   = Carbon::createFromFormat('H:i', $this->end_time);

            // 出勤時間 > 退勤時間のチェック
            if ($start->gte($end)) {
                $validator->errors()->add('end_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩時間のチェック
            foreach ($this->breaks as $i => $break) {
                if ($break['start'] && $break['end']) {
                    $breakStart = Carbon::createFromFormat('H:i', $break['start']);
                    $breakEnd = Carbon::createFromFormat('H:i', $break['end']);

                    // 休憩開始時間が出勤時間より前、または退勤時間より後
                    if ($breakStart->lt($start) || $breakEnd->gt($end)) {
                        $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                    }

                    // 休憩終了時間が退勤時間より後
                    if ($breakEnd->gt($end)) {
                        $validator->errors()->add("breaks.$i.end", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
