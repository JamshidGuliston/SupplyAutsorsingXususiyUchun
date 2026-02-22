@extends('layouts.app')

@section('leftmenu')
@include('casher.sidemenu'); 
@endsection
@section('css')
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
</style>
@endsection

@section('content')
<!-- Edite -->
<div class="modal editesmodal fade" id="Modalgarden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('technolog.bindgarden')}}" method="post">
                @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Katta xarajatlar turi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                
                <div id="ghidden"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-warning">Saqlash</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('casher.deletecash')}}" method="post">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="deletename"></div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn dele btn-danger">O'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="py-4 px-4">
    <form method="POST" action="{{route('casher.createcash')}}">
        @csrf
        <div class="form-group row">
            <div class="col-md-3">
                <select class="form-select" name="catid" aria-label="Default select example">
                    @foreach($allcosts as $row)
                    <option value="{{$row['id']}}">{{$row['allcost_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="dayid" aria-label="Default select example">
                    @foreach($days as $row)
                    <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="value" class="form-control" placeholder="so'm" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="description" class="form-control" placeholder="izoh" required>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </div>

    </form>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Izoh</th>
                <th scope="col">Turi</th>
                <th scope="col">Sana</th>
                <th scope="col">So'm</th>
                <th scope="col">Holati</th>
                <th scope="col">...</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($cashes as $row)
                <tr>
                    <td>{{ $row->cashid }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->allcost_name }}</td>
                    <td>{{ $row->day_number.'/'.$row->month_name.'/'.$row->year_name }}</td>
                    <td>{{ $row->summ }} so'm</td>
                    @if($row->status == 1)
                        <td><p><i class="fas fa-clock"></i></p></td>
                        <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $row->description }}" data-delet-id="{{ $row->cashid }}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i></td>
                    @else
                        <td><i class="fas fa-check"></i></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $cashes->links() }}
    <br>
    <a href="/casher/home">Orqaga</a>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            var div = $('#hiddenid');
            div.html("<input type='hidden' name='id' value="+g+">");
        });

        $('.detete').click(function() {
            deleteid = $(this).attr('data-delet-id');
            pro_name = $(this).attr('data-name-id');
            var div = $('.deletename');
            // alert(deletes);
            div.html("<p>"+pro_name+".</p><input type='hidden' name='cashid' value="+deleteid+">");
            
        });
    });
    function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
</script>
@if(session('status'))
<script> 
    // alert('{{ session("status") }}');
    swal({
        title: "Ajoyib!",
        text: "{{ session('status') }}",
        icon: "success",
        button: "ok",
    });
</script>
@endif
@endsection