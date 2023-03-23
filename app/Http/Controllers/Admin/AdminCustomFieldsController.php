<?php

namespace App\Http\Controllers\Admin;

use App\ClientDetails;
use App\CustomField;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Requests\CustomField\StoreCustomField;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminCustomFieldsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.customFields';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.custom-fields.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->customFieldGroups = DB::table('custom_field_groups')->where('company_id', company()->id)->get()->pluck('name', 'id');

        return view('admin.custom-fields.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomField $request)
    {
        if ($request->module == 1) {
            $model = new ClientDetails();
        } elseif ($request->module == 2) {
            $model = new EmployeeDetails();
        } else {
            $model = new Project();
        }

        $group = [
            'fields' => [
                [
                    'name'     => $request->get('name'),
                    'groupID'  => $request->module,
                    'label'    => $request->get('label'),
                    'type'     => $request->get('type'),
                    'required' => $request->get('required'),
                    'show_employee' => ($request->show_employee == 'yes') ? 1 : 0,
                    'values'   => $request->get('value'),
                ]
            ],

        ];
        $model->addCustomField($group);
        return Reply::success('messages.customFieldCreateSuccess');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->field = CustomField::find($id);
        $this->field->values = json_decode($this->field->values);
        return view('admin.custom-fields.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $field = CustomField::find($id);
        $field->label = $request->label;
        $field->name = $request->name;
        $field->values = json_encode($request->value);
        $field->required = $request->required;
        $field->show_employee = ($request->show_employee == 'yes') ? 1 : 0;
        $field->save();
        return Reply::success('messages.updateSuccess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('custom_fields')->delete($id);
        return Reply::success('messages.deleteSuccess');
    }

    public function getFields()
    {
        $permissions = DB::table('custom_fields')
            ->join('custom_field_groups', 'custom_field_groups.id', '=', 'custom_fields.custom_field_group_id')
            ->select('custom_fields.id', 'custom_field_groups.name as module', 'custom_fields.label', 'custom_fields.show_employee', 'custom_fields.name', 'custom_fields.type', 'custom_fields.values', 'custom_fields.required')
            ->where('custom_field_groups.company_id', company()->id);
        $data = DataTables::of($permissions)
            ->editColumn(
                'values',
                function ($row) {
                    $ul = '';

                    if (isset($row->values) && $row->values != '[null]') {
                        $ul = '<ul>';
                        foreach (json_decode($row->values) as $key => $value) {
                            $ul .= '<li>' . $value . '</li>';
                        }
                    }

                    $ul .= '</ul>';

                    return $ul;
                }
            )
            ->editColumn(
                'required',
                function ($row) {
                    // Edit Button
                    $string = ' - ';
                    $class  = 'label bg-red label-danger disabled color-palette';

                    if ($row->required === 'yes') {
                        $string = '<span class="' . $class . '">' . $row->required . '</span>';
                    }

                    if ($row->required === 'no') {
                        $class  = 'label bg-red label-info disabled color-palette';
                        $string = '<span class="' . $class . '">' . $row->required . '</span>';
                    }

                    return $string;
                }
            )
            ->editColumn(
                'show_employee',
                function ($row) {
                    // Edit Button
                    $string = ' - ';
                    $class  = 'label bg-green label-success disabled color-palette';

                    if ($row->show_employee == 1) {
                        $string = '<span class="' . $class . '">' . __('app.yes') . '</span>';
                    }else {
                        $class  = 'label bg-red label-info disabled color-palette';
                        $string = '<span class="' . $class . '">' .  __('app.no')  . '</span>';
                    }

                    return $string;
                }
            )
            ->addColumn(
                'action',
                function ($row) {
                    return '<a href="javascript:;" class="btn btn-info btn-outline edit-custom-field"
                    data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>  <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
            )
            ->rawColumns(['values', 'action', 'required', 'show_employee'])
            ->make(true);
        return $data;
    }

}
