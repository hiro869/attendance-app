@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endpush

@section('content')
<div class="staff-page">
    <div class="staff-wrapper">

        {{-- タイトル --}}
        <div class="staff-title">
            <span class="title-bar"></span>
            <h2>スタッフ一覧</h2>
        </div>

        {{-- テーブル --}}
        <div class="staff-table-wrapper">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>月次勤怠</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffs as $staff)
                        <tr>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>
                                <a href="{{ route('admin.staff.attendance', $staff->id) }}">
                                    詳細
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
