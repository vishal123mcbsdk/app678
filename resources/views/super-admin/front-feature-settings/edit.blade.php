<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.featureSetting.editFeature')</h4>
</div>

{!!  Form::open(['url' => '' ,'method' => 'put', 'id' => 'add-edit-form','class'=>'form-horizontal']) 	 !!}
<div class="modal-body">
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">@lang('app.language')</label>
            <div class="col-sm-10">
                <select name="language" class="form-control selectpicker" id="language_switcher">
                    <option @if(is_null($feature->language_setting_id)) selected @endif value="0" data-content=" <span class='flag-icon flag-icon-us'></span> En"></option>
                    @forelse ($activeLanguages as $language)
                        <option
                                @if($language->id === $feature->language_setting_id) selected @endif
                        value="{{ $language->id }}" data-content=' <span class="flag-icon flag-icon-{{ $language->language_code }}"></span> {{ ucfirst($language->language_code) }}'>
                        </option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">@lang('app.title')</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="title" value="{{ $feature->title }}">
                <div class="form-control-focus"> </div>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">@lang('app.description')</label>
            <div class="col-sm-10">
                <textarea type="text" class="form-control summernote" id="description" rows="3" name="description" > {!! $feature->description !!} </textarea>
                <div class="form-control-focus"> </div>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button id="save" type="button" class="btn btn-custom">@lang('app.submit')</button>
</div>
{{ Form::close() }}
<script src="{{ asset('plugins/iconpicker/js/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script>
    $('.selectpicker').selectpicker();

    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        dialogsInBody: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link',  'hr','video']],
            ['view', ['fullscreen']],
            ['help', ['help']]
        ]
    });
    $(function () {
        $('.icp-dd').iconpicker({
            title: 'Dropdown with picker',
            component:'.btn > i',
            templates: {
                iconpickerItem: '<a role="button" href="javascript:;" class="iconpicker-item"><i></i></a>'
            }
        });

        $('.icp').on('iconpickerSelected', function (e) {
            $('#iconInput').val(e.iconpickerValue);
            $('#iconButton').html('<i  class="'+e.iconpickerValue+'"></i></button>');
        });
    });
    $('#save').click(function () {
        var url = '{{ route('super-admin.front-feature-settings.update', $feature->id)}}';
        $.easyAjax({
            url: url,
            container: '#add-edit-form',
            type: "POST",
            file:true,
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
        return false;
    })
</script>

