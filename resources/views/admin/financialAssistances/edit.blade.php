@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.financialAssistance.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.financial-assistances.update", [$financialAssistance->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="problem_presented">{{ trans('cruds.financialAssistance.fields.problem_presented') }}</label>
                <input class="form-control {{ $errors->has('problem_presented') ? 'is-invalid' : '' }}" type="text" name="problem_presented" id="problem_presented" value="{{ old('problem_presented', $financialAssistance->problem_presented) }}">
                @if($errors->has('problem_presented'))
                    <span class="text-danger">{{ $errors->first('problem_presented') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.problem_presented_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="date_interviewed">{{ trans('cruds.financialAssistance.fields.date_interviewed') }}</label>
                <input class="form-control datetime {{ $errors->has('date_interviewed') ? 'is-invalid' : '' }}" type="text" name="date_interviewed" id="date_interviewed" value="{{ old('date_interviewed', $financialAssistance->date_interviewed) }}">
                @if($errors->has('date_interviewed'))
                    <span class="text-danger">{{ $errors->first('date_interviewed') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_interviewed_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="assessment">{{ trans('cruds.financialAssistance.fields.assessment') }}</label>
                <input class="form-control {{ $errors->has('assessment') ? 'is-invalid' : '' }}" type="text" name="assessment" id="assessment" value="{{ old('assessment', $financialAssistance->assessment) }}">
                @if($errors->has('assessment'))
                    <span class="text-danger">{{ $errors->first('assessment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.assessment_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="recommendation">{{ trans('cruds.financialAssistance.fields.recommendation') }}</label>
                <input class="form-control {{ $errors->has('recommendation') ? 'is-invalid' : '' }}" type="text" name="recommendation" id="recommendation" value="{{ old('recommendation', $financialAssistance->recommendation) }}">
                @if($errors->has('recommendation'))
                    <span class="text-danger">{{ $errors->first('recommendation') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.recommendation_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="amount">{{ trans('cruds.financialAssistance.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="text" name="amount" id="amount" value="{{ old('amount', $financialAssistance->amount) }}">
                @if($errors->has('amount'))
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="scheduled_fa">{{ trans('cruds.financialAssistance.fields.scheduled_fa') }}</label>
                <input class="form-control {{ $errors->has('scheduled_fa') ? 'is-invalid' : '' }}" type="text" name="scheduled_fa" id="scheduled_fa" value="{{ old('scheduled_fa', $financialAssistance->scheduled_fa) }}">
                @if($errors->has('scheduled_fa'))
                    <span class="text-danger">{{ $errors->first('scheduled_fa') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.scheduled_fa_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="status">{{ trans('cruds.financialAssistance.fields.status') }}</label>
                <input class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" type="text" name="status" id="status" value="{{ old('status', $financialAssistance->status) }}">
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="date_claimed">{{ trans('cruds.financialAssistance.fields.date_claimed') }}</label>
                <input class="form-control {{ $errors->has('date_claimed') ? 'is-invalid' : '' }}" type="text" name="date_claimed" id="date_claimed" value="{{ old('date_claimed', $financialAssistance->date_claimed) }}">
                @if($errors->has('date_claimed'))
                    <span class="text-danger">{{ $errors->first('date_claimed') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_claimed_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.financialAssistance.fields.note') }}</label>
                <input class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" type="text" name="note" id="note" value="{{ old('note', $financialAssistance->note) }}">
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="requirements">{{ trans('cruds.financialAssistance.fields.requirements') }}</label>
                <div class="needsclick dropzone {{ $errors->has('requirements') ? 'is-invalid' : '' }}" id="requirements-dropzone">
                </div>
                @if($errors->has('requirements'))
                    <span class="text-danger">{{ $errors->first('requirements') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.financialAssistance.fields.requirements_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    var uploadedRequirementsMap = {}
Dropzone.options.requirementsDropzone = {
    url: '{{ route('admin.financial-assistances.storeMedia') }}',
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 10
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="requirements[]" value="' + response.name + '">')
      uploadedRequirementsMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedRequirementsMap[file.name]
      }
      $('form').find('input[name="requirements[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($financialAssistance) && $financialAssistance->requirements)
          var files =
            {!! json_encode($financialAssistance->requirements) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="requirements[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection