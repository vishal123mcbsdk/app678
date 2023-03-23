<?php

namespace App\Http\Controllers\Client;

use App\Helper\Reply;
use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;
use App\ModuleSetting;
use App\Notifications\ReplyTicket;
use App\Ticket;
use App\TicketReply;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class ClientTicketsController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tickets';
        $this->pageIcon = 'ti-ticket';
        $this->middleware(function ($request, $next) {
            if(!in_array('tickets', $this->user->modules)){
                abort(403);
            }
            return $next($request);
        });

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.tickets.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->upload = can_upload();
        return view('client.tickets.create', $this->data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = new Ticket();
        $ticket->subject = $request->subject;
        $ticket->user_id = $this->user->id;
        $ticket->save();

        //save first message
        $reply = new TicketReply();
        $reply->message = $request->description;
        $reply->ticket_id = $ticket->id;
        $reply->user_id = $this->user->id; //current logged in user
        $reply->save();

        return Reply::successWithData(__('messages.ticketAddSuccess'), ['ticketReplyID' => $reply->id]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->ticket = Ticket::where('user_id', $this->user->id)->findOrFail($id);

        $this->upload = can_upload();
        return view('client.tickets.edit', $this->data);
    }

    /**
     * @param UpdateTicketRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateTicketRequest $request, $id)
    {
        $reply = new TicketReply();
        $reply->message = $request->message;
        $reply->ticket_id = $id;
        $reply->user_id = $this->user->id; //current logged in user
        $reply->save();
        $this->reply = $reply;
        $lastMessage = view('client.tickets.last-message', $this->data)->render();
        return Reply::dataOnly(['lastMessage' => $lastMessage,'ticketReplyID' => $reply->id]);
        //        return Reply::successWithData(__('messages.ticketReplySuccess'), ['lastMessage' => $lastMessage]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function data()
    {
        $tickets = Ticket::where('user_id', $this->user->id)->get();

        return DataTables::of($tickets)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('client.tickets.edit', $row->id) . '" class="btn btn-info" ><i class="fa fa-eye"></i> ' . __('modules.client.viewDetails') . '</a>';
            })
            ->editColumn('subject', function ($row) {
                return ucfirst($row->subject);
            })
            ->editColumn('agent_id', function ($row) {
                if (!is_null($row->agent_id)) {
                    return ucwords($row->agent->name);
                }
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'open') {
                    return '<label class="label label-danger">' . $row->status . '</label>';
                }
                elseif ($row->status == 'pending') {
                    return '<label class="label label-warning">' . $row->status . '</label>';
                }
                elseif ($row->status == 'resolved') {
                    return '<label class="label label-info">' . $row->status . '</label>';
                }
                elseif ($row->status == 'closed') {
                    return '<label class="label label-success">' . $row->status . '</label>';
                }
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format($this->global->date_format.' '.$this->global->time_format);
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format($this->global->date_format.' '.$this->global->time_format);
            })
            ->rawColumns(['action', 'status'])
            ->removeColumn('user_id')
            ->removeColumn('channel_id')
            ->removeColumn('type_id')
            ->removeColumn('deleted_at')
            ->make(true);
    }

    public function closeTicket($id)
    {
        $ticket = Ticket::where('user_id', $this->user->id)->findOrFail($id);
        $ticket->status = 'closed';
        $ticket->close_date = Carbon::now()->format('Y-m-d');
        $ticket->save();

        $reply = new TicketReply();
        $reply->message = 'Ticket <strong>closed</strong> by ' . ucwords($this->user->name);
        $reply->ticket_id = $id;
        $reply->user_id = $this->user->id; //current logged in user
        $reply->save();

        return Reply::redirect(route('client.tickets.index'), __('messages.ticketReplySuccess'));
    }

    public function reopenTicket($id)
    {
        $ticket = Ticket::where('user_id', $this->user->id)->findOrFail($id);
        if(is_null($ticket->agent_id)){
            $ticket->status = 'open';
        }
        else{
            $ticket->status = 'pending';
        }
        $ticket->save();

        $reply = new TicketReply();
        $reply->message = 'Ticket <strong>reopend</strong> by ' . ucwords($this->user->name);
        $reply->ticket_id = $id;
        $reply->user_id = $this->user->id; //current logged in user
        $reply->save();

        return Reply::redirect(route('client.tickets.index'), __('messages.ticketReplySuccess'));
    }

}
