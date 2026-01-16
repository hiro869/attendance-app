@extends('layouts.admin')
@push('page_css')
<link rel="stylesheet" href="{{ asset('css/request/list.css') }}">
@endpush

@section('content')
<div class="page-wrapper">

    <h2 class="page-title">
        <span class="title-bar"></span>
        申請一覧
    </h2>

    <div class="tab-menu">
        <a href="{{ route('admin.request.list', ['tab' => 'pending']) }}"
           class="{{ $tab === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.request.list', ['tab' => 'approved']) }}"
           class="{{ $tab === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <table class="list-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $req)
            <tr>
                <td>{{ $req->status === 0 ? '承認待ち' : '承認済み' }}</td>
                <td>{{ $req->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($req->attendance->work_date)->format('Y/m/d') }}</td>
                <td>{{ $req->note }}</td>
                <td>{{ $req->created_at->format('Y/m/d') }}</td>
                <td>
                    <a href="{{ route('admin.request.approve', $req->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
