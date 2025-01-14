<?php

/*
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Auth;
use App\User;
use App\TicketModel;
use App\GroupsModel;
use App\AgentModel;
use App\ClientModel;
use App\CaptchaModel;
use App\FaqModel;
use App\BgImagesModel;
use App\WorkSpaceModel;
use App\HomeFaqModel;
use App\TicketsHaveTypes;
use App\MailserviceModel;
use App\PushModel;
use App\MailerModel;

/**
 * Class MainController
 *
 * Perform general computations
 */
class MainController extends Controller
{
    /**
     * Return either agent dashboard or ticket creation dashboard
     *
     * @param string $workspace
     * @return Illuminate\View\View
     */
    public function workspaceIndex($workspace)
    {
        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            $ws = WorkSpaceModel::where('slug', '=', $workspace)->where('deactivated', '=', false)->first();
            if ($ws === null) {
                return redirect('/')/*->with('error', __('app.workspace_not_found_or_deactivated'))*/;
            }
        }

        if ((Auth::guest()) || (request('v') === 'c')) {
            \App::setLocale($ws->lang);

            $img = BgImagesModel::queryRandomImage($ws->id);

            $captchadata = CaptchaModel::createSum(session()->getId());

            $infomessage = $ws->welcomemsg;
            if ($infomessage === '') {
                $infomessage = __('app.ticket_creation_welcomemsg');
            }
            $infomessage = strip_tags($infomessage, env('APP_ALLOWEDHTMLTAGS'));

            return view('dashboard_customer', ['workspace' => $ws->name, 'wsobject' => $ws, 'bgimage' => $img, 'captchadata' => $captchadata, 'ticketTypes' => TicketsHaveTypes::where('workspace', '=', $ws->id)->get(), 'faqs' => FaqModel::where('workspace', '=', $ws->id)->get(), 'infomessage' => $infomessage, 'allowattachments' => $ws->allowattachments]);
        } else {
            $tickets = TicketModel::queryAgentTickets(User::getAgent(auth()->id())->id);
            $agentUser = User::getAgent(auth()->id());
            $groups = array();
            foreach ($tickets as $ticketkey => &$ticket)
            {
                if (($agentUser->hideclosedtickets) && ($ticket->status == 3)) {
                    unset($tickets[$ticketkey]);
                    continue;
                }

                $item = array();
                $item['ticket_id'] = $ticket->id;
                $item['group_name'] = GroupsModel::get($ticket->group)->name;
                array_push($groups, $item);
            }
            
            $typeCounts = array();
            $ticketTypes = TicketsHaveTypes::where('workspace', '=', $ws->id)->get();
            foreach ($ticketTypes as $ticketType) {
                $item = array();
                $item['name'] = $ticketType->name;
                $item['count'] = TicketModel::where('workspace', '=', $ws->id)->where('type', '=', $ticketType->id)->count();
                $typeCounts[] = $item;
            }

            $stats_start = date('Y-m-d', strtotime('-7 days'));
            $stats_end = date('Y-m-d');
            $stats_diff = (new \DateTime($stats_end))->diff((new \DateTime($stats_start)))->format('%a');
            
            $stats = \DB::table((new TicketModel)->getTable())->select(\DB::raw('DATE(created_at) AS created_at, COUNT(hash) AS count'))->where('workspace', '=', $ws->id)->whereRaw('DATE(created_at) > ?', [$stats_start])->whereRaw('DATE(created_at) <= ?', [$stats_end])->groupBy(\DB::raw('DATE(created_at)'))->orderBy('created_at', 'ASC')->get();
            
            return view('dashboard_agent', [
                'workspace' => $ws->name,
                'location' => __('app.dashboard'),
                'user' => User::get(auth()->id()),
                'agent' => User::getAgent(auth()->id()),
                'serving' => TicketModel::where('workspace', '=', $ws->id)->count(),
                'yours' => TicketModel::where('workspace', '=', $ws->id)->where('assignee', '=', User::getAgent(auth()->id())->id)->count(),
                'typeCounts' => $typeCounts,
                'groups' => GroupsModel::where('workspace', '=', $ws->id)->count(),
                'superadmin' => User::getAgent(auth()->id())->superadmin,
                'agents' => AgentModel::where('workspace', '=', $ws->id)->count(),
                'tickets' => $tickets,
                'groupnames' => $groups,
                'stats' => $stats,
                'stats_start' => $stats_start,
                'stats_end' => $stats_end,
                'stats_diff' => (int)$stats_diff
            ]);
        }
    }

    /**
     * Default service landing page
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id())->workspace)->first();
            return redirect('/' . $ws->name);
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

		if (env('APP_SHOWSTATISTICS')) {
		    $timeToKeepCache = (int)env('APP_STATSCACHETIME', 60 * 24) * 60;

			$count_workspaces = Cache::remember('count_workspaces', $timeToKeepCache, function() { return WorkSpaceModel::count(); });
			$count_tickets = Cache::remember('count_tickets', $timeToKeepCache, function() { return TicketModel::count(); });
			$count_agents = Cache::remember('count_agents', $timeToKeepCache, function() { return AgentModel::count(); });
			$count_clients = Cache::remember('count_clients', $timeToKeepCache, function() { return TicketModel::distinct('email')->count('email'); });
		} else {
            $count_workspaces = null;
            $count_tickets = null;
            $count_agents = null;
            $count_clients = null;
        }

        if (isset($_COOKIE['clep'])) {
            session()->reflash();

            return redirect('/clep/index');
        }

        $donationCode = null;

        if (file_exists(public_path() . '/data/donation.txt')) {
            $donationCode = Cache::remember('donation_code', 3600, function() {
                return file_get_contents(public_path() . '/data/donation.txt');
            });
        }

        return view('home', [
            'captchadata' => $captchadata,
            'count_workspaces' => $count_workspaces,
            'count_tickets' => $count_tickets,
            'count_agents' => $count_agents,
            'count_clients' => $count_clients,
            'donationCode' => $donationCode
        ]);
    }

    /**
     * View API page
     *
     * @return mixed
     */
    public function api()
    {
        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('api', ['captchadata' => $captchadata]);
    }

    /**
     * View faq page
     *
     * @return mixed
     */
    public function faq()
    {
        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('faq', ['captchadata' => $captchadata, 'faqs' => HomeFaqModel::getAll()]);
    }

    /**
     * View imprint page
     *
     * @return mixed
     */
    public function imprint()
    {
        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('imprint', ['captchadata' => $captchadata]);
    }

    /**
     * View tac page
     *
     * @return mixed
     */
    public function tac()
    {
        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('tac', ['captchadata' => $captchadata]);
    }

    /**
     * Perform login
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        if (Auth::guest()) {
            $attr = request()->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', '=', $attr['email'])->first();
            if ($user !== null) {
                if ($user->account_confirm !== '_confirmed') {
                    return back()->with('error', __('app.account_not_yet_confirmed'));
                }

                if ($user->deactivated) {
                    return back()->with('error', __('app.account_deactivated'));
                }
            }

            if (Auth::attempt([
                'email' => $attr['email'],
                'password' => $attr['password']
            ])) {
                $entity = User::getAgent(auth()->id());
                if ($entity) {
                    if (!$entity->active) {
                        Auth::logout();
                        request()->session()->invalidate();

                        return redirect('/')->with('error', __('app.agent_is_inactive'));
                    }
                }

                $ws = WorkSpaceModel::where('id', '=', $entity->workspace)->where('deactivated', '=', false)->first();
                if ($ws === null) {
                    Auth::logout();
                    request()->session()->invalidate();

                    return back()->with('error', __('app.workspace_not_found_or_deactivated'));
                }

                return redirect('/' . $ws->name);
            } else {
                return redirect('/')->with('error', __('app.invalid_credentials'));
            }
        } else {
            return redirect('/')->with('error', __('app.login_already_done'));
        }
    }

    /**
     * Perform logout
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        if(Auth::check()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id())->workspace)->first();

            Auth::logout();
            request()->session()->invalidate();

            return  redirect('/')->with('success', __('app.logout_success'));
        } else {
            return  redirect('/')->with('error', __('app.not_logged_in'));
        }
    }

    /**
     * Send email with password recovery link to user
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function recover()
    {
        $attr = request()->validate([
            'email' => 'required|email'
        ]);

        $user = User::getByEmail($attr['email']);
        if (!$user) {
            return back()->with('error', __('app.email_not_found'));
        }

        $entity = User::getAgent($user->id);

        $user->password_reset = md5($user->email . date('c') . uniqid('', true));
        $user->save();

        $htmlCode = view('mail.pwreset', ['name' => $entity->firstname . ' ' . $entity->lastname, 'hash' => $user->password_reset])->render();
        MailerModel::sendMail($user->email, '[' . env('APP_NAME') . '] ' . __('app.mail_password_reset_subject'), $htmlCode);

        return back()->with('success', __('app.pw_recovery_ok'));
    }

    /**
     * Password reset view
     *
     * @return Illuminate\View\View
     */
    public function viewReset()
    {
        $img = 'bg' . random_int(1, 4) . '.jpg';

        return view('resetpw', [
            'bgimage' => $img,
            'hash' => request('hash'),
            'workspace' => ''
        ]);
    }

    /**
     * Reset new password
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        $attr = request()->validate([
            'password' => 'required',
            'password_confirm' => 'required'
        ]);

        if ($attr['password'] != $attr['password_confirm']) {
            return back()->with('error', __('app.password_mismatch'));
        }

        $user = User::where('password_reset', '=', request('hash'))->first();
        if (!$user) {
            return redirect('/')->with('error', __('app.hash_not_found'));
        }

        $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
        $user->password_reset = '';
        $user->save();

        return redirect('/')->with('success', __('app.password_reset_ok'));
    }

    /**
     * Process registration
     *
     * @return mixed
     */
    public function register()
    {
        $attr = request()->validate([
            'company' => 'required',
            'fullname' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required',
            'captcha' => 'required|numeric'
        ]);

        $attr['lang'] = 'en';
        $attr['usebgcolor'] = false;
        $attr['bgcolorcode'] = 'E5E5E6';
        $attr['welcomemsg'] = __('app.system_welcomemsg');

        $attr['name'] = md5($attr['fullname'] . $attr['email'] . random_bytes(55));

        $workspace = WorkSpaceModel::get($attr['name']);
        if ($workspace !== null) {
            return back()->with('error', __('app.workspace_already_exists'));
        }

        $emailuser = User::getByEmail($attr['email']);
        if ($emailuser !== null) {
            return back()->with('error', __('app.email_already_in_use'));
        }

        if ($attr['captcha'] !== CaptchaModel::querySum(session()->getId())) {
            return back()->with('error', __('app.captcha_invalid'));
        }

        if ($attr['password'] !== $attr['password_confirmation']) {
            return back()->with('error', __('app.password_mismatch'));
        }

        $attr['apitoken'] = md5(random_bytes(55));

        $attr['formtitle'] = __('app.ticket_create');
        $attr['ticketcreatedmsg'] = __('app.ticket_created_customer_notconfirm');

        $attr['slug'] = $attr['name'];

        $workspace = WorkSpaceModel::create($attr);
        if ($workspace === null) {
            return back()->with('error', __('app.workspace_creation_failed'));
        }

        $workspace->slug = \Str::slug($attr['company'] . '-' . strval($workspace->id) . strval(rand(10, 100)));
        $workspace->paidforapi = !env('APP_PAYFORAPI');
        $workspace->save();

        $user = new \App\User;
        $user->workspace = $workspace->id;
        $user->name = $attr['name'];
        $user->email = $attr['email'];
        $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
        $user->account_confirm = md5($user->email . date('Y-m-d H:i:s') . random_bytes(55));
        $user->avatar = 'default.png';
        $user->user_id = 0;
        $user->language = 'en';
        $user->save();

        if (strpos($attr['fullname'], ' ') !== false) {
            $surname = substr($attr['fullname'], 0, strpos($attr['fullname'], ' '));
            $lastname = substr($attr['fullname'], strpos($attr['fullname'], ' ') + 1);
        } else {
            $surname = '';
            $lastname = $attr['fullname'];
        }

        $agent = new \App\AgentModel;
        $agent->workspace = $workspace->id;
        $agent->surname = $surname;
        $agent->lastname = $lastname;
        $agent->email = $attr['email'];
        $agent->superadmin = true;
        $agent->position = 'Administrator';
        $agent->user_id = $user->id;
        $agent->save();

        $user->user_id = $agent->id;
        $user->save();

        $group = new \App\GroupsModel;
        $group->workspace = $workspace->id;
        $group->name = '1st level group';
        $group->description = 'The primary group';
        $group->def = true;
        $group->save();

        $groupMember = new \App\AgentsHaveGroups;
        $groupMember->agent_id = $agent->id;
        $groupMember->group_id = $group->id;
        $groupMember->save();

        $ttServiceRequest = new \App\TicketsHaveTypes;
        $ttServiceRequest->workspace = $workspace->id;
        $ttServiceRequest->name = 'Service Request';
        $ttServiceRequest->save();

        $ttIncident = new \App\TicketsHaveTypes;
        $ttIncident->workspace = $workspace->id;
        $ttIncident->name = 'Incident';
        $ttIncident->save();

        $ttChange = new \App\TicketsHaveTypes;
        $ttChange->workspace = $workspace->id;
        $ttChange->name = 'Change';
        $ttChange->save();

        $htmlCode = view('mail.workspace_created', ['name' => $attr['fullname'], 'hash' => $user->account_confirm])->render();
        MailerModel::sendMail($attr['email'], '[' . env('APP_NAME') . '] Your Workspace', $htmlCode);

        return redirect('/')->with('success', __('app.signup_welcomemsg'));
    }

    /**
     * Confirm account
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function confirm()
    {
        $hash = request('hash');

        $user = User::where('account_confirm', '=', $hash)->first();
        if ($user === null) {
            return back()->with('error', __('app.account_confirm_token_not_found'));
        }

        $user->account_confirm = '_confirmed';
        $user->save();

        return redirect('/')->with('success', __('app.account_confirmed_ok'));
    }

    /**
     * Perform mailservice operations
     *
     * @param string $password
     * @return \Illuminate\Http\JsonResponse
     */
    public function mailservice($password)
    {
        if ($password === env('MAILSERV_CRONPW')) {
            $_ENV['TEMP_WORKSPACE'] = -1;

            $ms = new MailserviceModel;
            $result = $ms->processInbox();

            return response()->json(['code' => 200, 'data' => $result]);
        } else {
            return response()->json(['code' => 403, 'data' => array()]);
        }
    }

    /**
     * Perform mailservice operations
     *
     * @param string $password
     * @return \Illuminate\Http\JsonResponse
     */
    public function mailservice_custom($password)
    {
        if ($password === env('MAILSERV_CRONPW')) {
            $result = MailserviceModel::processWorkspaceInboxes();

            return response()->json(['code' => 200, 'data' => $result]);
        } else {
            return response()->json(['code' => 403, 'data' => array()]);
        }
    }

    /**
     * Client endpoint: landing page
     *
     * @return mixed
     */
    public function clep_index()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id())->workspace)->first();
            return redirect('/' . $ws->name);
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('clep.index', ['captchadata' => $captchadata]);
    }

    /**
     * Client endpoint: notifications
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clep_notifications()
    {
        if (Auth::guest()) {
            return response()->json(array('code' => 403));
        }

        return response()->json(array('code' => 200, 'data' => PushModel::getUnseenNotifications(auth()->id())));
    }

    /**
     * Client endpoint: statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clep_statistics()
    {
        if (Auth::guest()) {
            return response()->json(array('code' => 403));
        }

        $ws = WorkSpaceModel::where('id', '=', User::getAgent(auth()->id())->workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return response()->json(array('code' => 404, 'data' => __('app.workspace_not_found_or_deactivated')));
        }

        $typeCounts = array();
        $ticketTypes = TicketsHaveTypes::where('workspace', '=', $ws->id)->get();
        foreach ($ticketTypes as $ticketType) {
            $item = array();
            $item['name'] = $ticketType->name;
            $item['count'] = TicketModel::where('workspace', '=', $ws->id)->where('type', '=', $ticketType->id)->count();
            $typeCounts[] = $item;
        }

        $data = array(
            'serving' => TicketModel::where('workspace', '=', $ws->id)->count(),
            'yours' => TicketModel::where('workspace', '=', $ws->id)->where('assignee', '=', User::getAgent(auth()->id())->id)->count(),
            'agents' => AgentModel::where('workspace', '=', $ws->id)->count(),
            'groups' => GroupsModel::where('workspace', '=', $ws->id)->count(),
            'typeCounts' => $typeCounts,
        );

        return response()->json(array('code' => 200, 'data' => $data));
    }

    /**
     * Client endpoint: statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clep_devicetoken()
    {
        if (Auth::guest()) {
            return response()->json(array('code' => 403));
        }

        $device_token = request('token', null);
        if ((!is_string($device_token)) || (strlen($device_token) == 0)) {
            return response()->json(array('code' => 500));
        }

        $user = User::where('id', '=', auth()->id())->first();
        if (!$user) {
            return response()->json(array('code' => 500));
        }

        $user->device_token = $device_token;
        $user->save();

        return response()->json(array('code' => 200));
    }
}
