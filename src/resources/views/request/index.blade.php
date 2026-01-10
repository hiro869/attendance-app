@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/request/index.css') }}">
@endpush

@section('content')

<div class="request-page">

    <div class="request-wrapper">

        {{-- タイトル --}}
        <div class="request-title">
            <span class="title-bar"></span>
            <h2>申請一覧</h2>
        </div>

        {{-- タブ --}}
        <div class="request-tabs">
            <a href="{{ route('request.index', ['tab' => 'pending']) }}"
               class="tab {{ $tab === 'pending' ? 'active' : '' }}">
                承認待ち
            </a>

            <a href="{{ route('request.index', ['tab' => 'approved']) }}"
               class="tab {{ $tab === 'approved' ? 'active' : '' }}">
                承認済み
            </a>
        </div>

        {{-- テーブル --}}
        <div class="request-card">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>{{ $tab === 'pending' ? '申請日時' : '承認日時' }}</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $tab === 'pending' ? '承認待ち' : '承認済み' }}</td>
                        <td>{{ $req->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($req->attendance->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $req->note }}</td>
                        <td>
                            {{ $tab === 'pending'
                                ? $req->created_at->format('Y/m/d')
                                : $req->approved_at->format('Y/m/d') }}
                        </td>
                        <td>
                            <a class="detail-link"
                               href="{{ route('attendance.detail', $req->attendance_id) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            {{ $tab === 'pending' ? '承認待ちはありません' : '承認済みはありません' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
