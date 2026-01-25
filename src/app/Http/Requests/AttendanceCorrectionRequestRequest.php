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

            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->start_time || !$this->end_time) {
                return;
            }

            $workStart = Carbon::createFromFormat('H:i', $this->start_time);
            $workEnd   = Carbon::createFromFormat('H:i', $this->end_time);

            /* ① 出勤・退勤 */
            if ($workStart->gte($workEnd)) {
                $validator->errors()->add(
                    'end_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
                return; // ★ 他は見ない
            }

            foreach ($this->breaks ?? [] as $i => $break) {

                if (empty($break['start']) || empty($break['end'])) {
                    continue;
                }

                $bs = Carbon::createFromFormat('H:i', $break['start']);
                $be = Carbon::createFromFormat('H:i', $break['end']);

                /* ② 休憩開始 */
                if ($bs->lt($workStart) || $bs->gte($workEnd)) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                    continue; // ★ 1休憩1エラー
                }

                /* ③ 休憩終了 */
                if ($be->gt($workEnd)) {
                    $validator->errors()->add(
                        "breaks.$i.end",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                    continue;
                }

                /* 休憩終了 ≤ 開始 */
                if ($be->lte($bs)) {
                    $validator->errors()->add(
                        "breaks.$i.end",
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }
}
