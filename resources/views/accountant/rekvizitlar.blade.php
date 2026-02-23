@extends('layouts.app')

@section('leftmenu')
@include('accountant.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
        <div class="col-12">
            <h4 class="fw-bold">Bog'cha rekvizitlar</h4>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Yangi rekvizit qo'shish formasi -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Yangi rekvizit qo'shish</div>
        <div class="card-body">
            <form action="{{ route('accountant.rekvizit.store') }}" method="POST">
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
                    <div class="col-md-3">
                        <label class="form-label">Direktor F.I.O</label>
                        <input type="text" name="director_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Direktor telefon</label>
                        <input type="text" name="director_phone" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qabulxona telefon</label>
                        <input type="text" name="reception_phone" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Manzil</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">INN</label>
                        <input type="text" name="inn" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">X/R (hisob raqam)</label>
                        <input type="text" name="bank_account" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">MFO</label>
                        <input type="text" name="mfo" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Yagona g'azna x/r</label>
                        <input type="text" name="treasury_account" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bank</label>
                        <input type="text" name="bank" class="form-control">
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

    <!-- Rekvizitlar jadvali -->
    <div class="card">
        <div class="card-header fw-bold">Rekvizitlar ro'yxati</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:13px">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Bog'cha</th>
                            <th>Direktor</th>
                            <th>Dir. tel</th>
                            <th>Qabul tel</th>
                            <th>Manzil</th>
                            <th>INN</th>
                            <th>X/R</th>
                            <th>MFO</th>
                            <th>Yagona g'azna</th>
                            <th>Bank</th>
                            <th>Boshlanish</th>
                            <th>Tugash</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekvizitlar as $i => $r)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $r->kindgarden->number_of_org ?? '-' }}-son</td>
                            <td>{{ $r->director_name }}</td>
                            <td>{{ $r->director_phone }}</td>
                            <td>{{ $r->reception_phone }}</td>
                            <td>{{ $r->address }}</td>
                            <td>{{ $r->inn }}</td>
                            <td>{{ $r->bank_account }}</td>
                            <td>{{ $r->mfo }}</td>
                            <td>{{ $r->treasury_account }}</td>
                            <td>{{ $r->bank }}</td>
                            <td>{{ $r->start_date ? $r->start_date->format('d.m.Y') : '-' }}</td>
                            <td>{{ $r->end_date ? $r->end_date->format('d.m.Y') : 'Ochiq' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRekvizit{{ $r->id }}">Tahrir</button>
                                <form action="{{ route('accountant.rekvizit.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('O\'chirasizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $r->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger">O'chir</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Tahrirlash modali -->
                        <div class="modal fade" id="editRekvizit{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Rekvizitni tahrirlash</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('accountant.rekvizit.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $r->id }}">
                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label">Bog'cha</label>
                                                    <select name="kindgarden_id" class="form-select" required>
                                                        @foreach($kindgardens as $kg)
                                                        <option value="{{ $kg->id }}" {{ $kg->id == $r->kindgarden_id ? 'selected' : '' }}>{{ $kg->number_of_org }}-son</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Direktor F.I.O</label>
                                                    <input type="text" name="director_name" class="form-control" value="{{ $r->director_name }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Direktor telefon</label>
                                                    <input type="text" name="director_phone" class="form-control" value="{{ $r->director_phone }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Qabulxona telefon</label>
                                                    <input type="text" name="reception_phone" class="form-control" value="{{ $r->reception_phone }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Manzil</label>
                                                    <input type="text" name="address" class="form-control" value="{{ $r->address }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">INN</label>
                                                    <input type="text" name="inn" class="form-control" value="{{ $r->inn }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">X/R (hisob raqam)</label>
                                                    <input type="text" name="bank_account" class="form-control" value="{{ $r->bank_account }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">MFO</label>
                                                    <input type="text" name="mfo" class="form-control" value="{{ $r->mfo }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Yagona g'azna x/r</label>
                                                    <input type="text" name="treasury_account" class="form-control" value="{{ $r->treasury_account }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Bank</label>
                                                    <input type="text" name="bank" class="form-control" value="{{ $r->bank }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Boshlanish sanasi</label>
                                                    <input type="date" name="start_date" class="form-control" value="{{ $r->start_date ? $r->start_date->format('Y-m-d') : '' }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tugash sanasi</label>
                                                    <input type="date" name="end_date" class="form-control" value="{{ $r->end_date ? $r->end_date->format('Y-m-d') : '' }}">
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
                        <tr><td colspan="14" class="text-center text-muted">Rekvizitlar yo'q</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
