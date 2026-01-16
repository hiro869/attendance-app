<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class MonthAttendanceController extends Controller
{
    public function index(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $current = $request->month
            ? Carbon::parse($request->month . '-01')
            : now()->startOfMonth();

        $prev = $current->copy()->subMonth()->format('Y-m');
        $next = $current->copy()->addMonth()->format('Y-m');

        $period = CarbonPeriod::create(
            $current->copy()->startOfMonth(),
            $current->copy()->endOfMonth()
        );

        // 🔥 修正ポイント：keyBy を「日付文字列」にする
        $attendances = Attendance::with('breaks')
            ->where('user_id', $staff->id)
            ->whereBetween('work_date', [
                $current->copy()->startOfMonth(),
                $current->copy()->endOfMonth()
            ])
            ->get()
            ->keyBy(fn ($att) => Carbon::parse($att->work_date)->toDateString());

        $week = ['日','月','火','水','木','金','土'];
        $rows = [];

        foreach ($period as $date) {
            $key = $date->toDateString();
            $att = $attendances[$key] ?? null;

            $start = $att?->start_time?->format('H:i') ?? 'ー';
            $end   = $att?->end_time?->format('H:i') ?? 'ー';

            $breakSec = 0;
            if ($att) {
                foreach ($att->breaks as $b) {
                    if ($b->break_start && $b->break_end) {
                        $breakSec += $b->break_start->diffInSeconds($b->break_end);
                    }
                }
            }

            $break = $breakSec
                ? sprintf('%d:%02d', intdiv($breakSec,3600), intdiv($breakSec%3600,60))
                : 'ー';

            if ($att && $att->start_time && $att->end_time) {
                $workSec = max(
                    $att->start_time->diffInSeconds($att->end_time) - $breakSec,
                    0
                );
                $total = sprintf('%d:%02d', intdiv($workSec,3600), intdiv($workSec%3600,60));
            } else {
                $total = 'ー';
            }

            $rows[] = [
                'attendance_id' => $att?->id,
                'date'     => $date->format('m/d') . '(' . $week[$date->dayOfWeek] . ')',
                'raw_date' => $key,
                'start'    => $start,
                'end'      => $end,
                'break'    => $break,
                'total'    => $total,
            ];
        }

        return view('admin.staff.attendance_list', compact(
            'staff','rows','current','prev','next'
        ));

    }
public function csv(Request $request, $id)
{
    $staff = User::findOrFail($id);

    $current = $request->month
        ? Carbon::parse($request->month . '-01')
        : now()->startOfMonth();

    $attendances = Attendance::with('breaks')
        ->where('user_id', $staff->id)
        ->whereBetween('work_date', [
            $current->copy()->startOfMonth(),
            $current->copy()->endOfMonth()
        ])
        ->orderBy('work_date')
        ->get();

    $fileName = $staff->name . '_' . $current->format('Y_m') . '_attendance.csv';

    return new StreamedResponse(function () use ($attendances) {
        $handle = fopen('php://output', 'w');

        // UTF-8 BOM（Excel対策）
        fwrite($handle, "\xEF\xBB\xBF");

        // ヘッダー
        fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

        foreach ($attendances as $att) {

            // 休憩合計（分）
            $breakSec = 0;
            foreach ($att->breaks as $b) {
                if ($b->break_start && $b->break_end) {
                    $breakSec += $b->break_start->diffInSeconds($b->break_end);
                }
            }

            $break = $breakSec
                ? sprintf('%d:%02d', intdiv($breakSec,3600), intdiv($breakSec%3600,60))
                : '';

            if ($att->start_time && $att->end_time) {
                $workSec = max(
                    $att->start_time->diffInSeconds($att->end_time) - $breakSec,
                    0
                );
                $total = sprintf('%d:%02d', intdiv($workSec,3600), intdiv($workSec%3600,60));
            } else {
                $total = '';
            }

            fputcsv($handle, [
                Carbon::parse($att->work_date)->format('Y/m/d'),
                optional($att->start_time)->format('H:i'),
                optional($att->end_time)->format('H:i'),
                $break,
                $total,
            ]);
        }

        fclose($handle);
    }, 200, [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename={$fileName}",
    ]);
}
}
