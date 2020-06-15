@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap mb-4">
            <form id="sort-form" class="form-inline" method="get">
                <div class="form-group">
                    <label class="col-form-label col-sm-4" for="sort">Сортувати: </label>
                    <select class="form-control col-sm-8" name="sort" id="sort" onchange="$('#sort-form').submit()">
                        <option @if(request()->input('sort') === 'name') selected @endif value="name">Ім'я</option>
                        <option @if(request()->input('sort') === 'surname') selected @endif value="surname">Прізвище</option>
                        <option @if(request()->input('sort') === 'address') selected @endif value="address">Адреса</option>
                        <option @if(request()->input('sort') === 'birth_date') selected @endif value="birth_date">Дата народження</option>
                        <option @if(request()->input('sort') === 'created_at') selected @endif value="created_at">Дата додавання</option>
                    </select>
                </div>
            </form>
            <a href="{{ route('addPatient') }}" class="btn btn-primary">Додати пацієнта</a>
        </div>
    
        @if($patients->isEmpty())
        <h3>Пацієнтів немає!</h3>
        @endif
        @foreach($patients as $patient)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $patient->getFullName() }}</strong>
                <a href="{{ route('editPatient', $patient->id) }}" class="close"><i class="fas fa-edit"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text mb-0"><strong>Email: </strong>{{ $patient->email }}</p>
                <p class="card-text mb-0"><strong>Номер телефону: </strong>{{ $patient->phone }}</p>
                <p class="card-text mb-0"><strong>Адреса: </strong>{{ $patient->address }}</p>
                <p class="card-text mb-0"><strong>Дата народження: </strong>{{ $patient->birth_date }}</p>
                <p class="card-text mb-0"><strong>Діагноз: </strong>{{ $patient->diagnosis->name }}</p>
                <p class="card-text"><strong>Призначений курс лікування: </strong>
                    <span>
                    @foreach($patient->treatments as $treatment)
                    {{ $treatment->course->prescription.($loop->last ? '' : ', ') }}
                    @endforeach
                    </span>
                    <a href="#modal" data-id="{{ $patient->id }}" data-toggle="modal"><i class="fas fa-plus">  Додати</i></a>
                </p>
            </div>
        </div>
        @endforeach
        {{ $patients->appends(request()->input())->links() }}
    </div>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Призначити курс лікування</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="course-select" action="{{ route('addTreatment', -1) }}" method="POST">
                        @csrf
                        <select class="form-control" name="course" id="course">
                            <option>...</option>
                            @foreach($courses as $course)
                            <option class="text-break" value="{{ $course->id }}">{{ $course->prescription }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
                    <button type="submit" class="btn btn-primary" form="course-select">Зберегти</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $('#modal').on('show.bs.modal', function(e){
        let id = $(e.relatedTarget).data('id');
        let url = $('#course-select').attr('action').replace('-1', id);
        $('#course-select').attr('action', url);
        
        let courses = $('#course>option').toArray();
        let a = $(e.relatedTarget).siblings('span').text().split(', ');
        courses.forEach(el => {
            $(el).attr('disabled', false);
            a.forEach(str => {
                if(str.trim() === $(el).text().trim()){
                    $(el).attr('disabled', true);
                }
            })
        });
    });
</script>
@endsection