<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') @lang('app.menu.contractType')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addContractType','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.name')</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-group" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>

    $('#addContractType').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.contract-type.store')}}',
            container: '#addContractType',
            type: "POST",
            data: $('#addContractType').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    $('#responsive-modal').modal('hide');
                    table._fnDraw();
                }
            }
        })
    });
</script>