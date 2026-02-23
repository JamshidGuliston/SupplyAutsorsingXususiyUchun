@extends('layouts.app')

@section('leftmenu')
@include('accountant.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
        <div class="col-12">
            <h4 class="fw-bold">Bog'cha shartnomalar</h4>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Yangi shartnoma qo'shish formasi -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Yangi shartnoma qo'shish</div>
        <div class="card-body">
            <form action="{{ route('accountant.shartnoma.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Bog'cha</label>
                        <select name="kindgarden_id" class="form-select" required>
                            <option value="">-- Tanlang --</option>
                            @foreach($kindgardens as $kg)
                            <option value="{{ $kg->id }}">{{ $kg->number_of_org }}-son</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Shartnoma raqami</label>
                        <input type="text" name="contract_number" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Shartnoma sanasi</label>
                        <input type="date" name="contract_date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Boshlanish sanasi</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tugash sanasi</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Qo'sh</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Shartnomalar jadvali -->
    <div class="card">
        <div class="card-header fw-bold">Shartnomalar ro'yxati</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Bog'cha</th>
                        <th>Shartnoma raqami</th>
                        <th>Shartnoma sanasi</th>
                        <th>Boshlanish</th>
                        <th>Tugash</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shartnomalar as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->kindgarden->number_of_org ?? '-' }}-son</td>
                        <td>{{ $s->contract_number }}</td>
                        <td>{{ $s->contract_date ? $s->contract_date->format('d.m.Y') : '-' }}</td>
                        <td>{{ $s->start_date ? $s->start_date->format('d.m.Y') : '-' }}</td>
                        <td>{{ $s->end_date ? $s->end_date->format('d.m.Y') : 'Ochiq' }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editShartnoma{{ $s->id }}">Tahrir</button>
                            <form action="{{ route('accountant.shartnoma.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('O\'chirasizmi?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $s->id }}">
                                <button type="submit" class="btn btn-sm btn-danger">O'chir</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Tahrirlash modali -->
                    <div class="modal fade" id="editShartnoma{{ $s->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Shartnomani tahrirlash</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('accountant.shartnoma.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $s->id }}">
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label">Bog'cha</label>
                                                <select name="kindgarden_id" class="form-select" required>
                                                    @foreach($kindgardens as $kg)
                                                    <option value="{{ $kg->id }}" {{ $kg->id == $s->kindgarden_id ? 'selected' : '' }}>{{ $kg->number_of_org }}-son</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Shartnoma raqami</label>
                                                <input type="text" name="contract_number" class="form-control" value="{{ $s->contract_number }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Shartnoma sanasi</label>
                                                <input type="date" name="contract_date" class="form-control" value="{{ $s->contract_date ? $s->contract_date->format('Y-m-d') : '' }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Boshlanish sanasi</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ $s->start_date ? $s->start_date->format('Y-m-d') : '' }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tugash sanasi</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ $s->end_date ? $s->end_date->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                                        <button type="submit" class="btn btn-primary">Saqlash</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">Shartnomalar yo'q</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
