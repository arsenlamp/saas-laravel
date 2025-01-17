<?php

/*
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

Route::get('/', 'MainController@index');
Route::get('/home', 'MainController@index');
Route::get('/faq', 'MainController@faq');
Route::get('/api', 'MainController@api');
Route::get('/imprint', 'MainController@imprint');
Route::get('/tac', 'MainController@tac');
Route::post('/login', 'MainController@login');
Route::any('/logout', 'MainController@logout');
Route::post('/recover', 'MainController@recover');
Route::get('/reset', 'MainController@viewReset');
Route::post('/reset', 'MainController@reset');
Route::get('/register', 'MainController@viewRegister');
Route::post('/register', 'MainController@register');
Route::get('/confirm', 'MainController@confirm');
Route::get('/clep/index', 'MainController@clep_index');
Route::get('/clep/notifications', 'MainController@clep_notifications');
Route::get('/clep/statistics', 'MainController@clep_statistics');
Route::any('/clep/device/token', 'MainController@clep_devicetoken');

Route::get('/{workspace}/agent/list', 'AgentController@viewList');
Route::get('/{workspace}/agent/{id}/show', 'AgentController@viewShow');
Route::get('/{workspace}/agent/create', 'AgentController@viewCreate');
Route::post('/{workspace}/agent/create', 'AgentController@createAgent');
Route::patch('/{workspace}/agent/{id}/edit', 'AgentController@editAgent');
Route::any('/{workspace}/agent/{id}/delete', 'AgentController@deleteAgent');
Route::patch('/{workspace}/agent/{id}/active/{status}', 'AgentController@setActiveStatus');
Route::patch('/{workspace}/agent/{agent}/group/{group}/add', 'AgentController@assignToGroup');
Route::any('/{workspace}/agent/{agent}/group/{group}/remove', 'AgentController@removeFromGroup');

Route::get('/{workspace}/ticket/create', 'TicketController@viewCreateTicket');
Route::post('/{workspace}/ticket/create', 'TicketController@createTicket');
Route::post('/{workspace}/ticket/create/agent', 'TicketController@createTicketAgent');
Route::get('/{workspace}/ticket/list', 'TicketController@viewTicketList');
Route::get('/{workspace}/ticket/{id}/show', 'TicketController@viewShowTicketAgent');
Route::get('/{workspace}/ticket/show/{hash}', 'TicketController@viewShowTicketClient');
Route::patch('/{workspace}/ticket/{id}/edit', 'TicketController@editTicket');
Route::any('/{workspace}/ticket/{id}/delete', 'TicketController@deleteTicket');
Route::patch('/{workspace}/ticket/{ticket}/assign/agent/{agent}', 'TicketController@assignToAgent');
Route::patch('/{workspace}/ticket/{ticket}/assign/group/{group}', 'TicketController@assignToGroup');
Route::patch('/{workspace}/ticket/{id}/status/{status}', 'TicketController@setStatus');
Route::patch('/{workspace}/ticket/{id}/type/{type}', 'TicketController@setType');
Route::patch('/{workspace}/ticket/{id}/prio/{prio}', 'TicketController@setPriority');
Route::post('/{workspace}/ticket/{id}/comment/add', 'TicketController@addCommentAgent');
Route::post('/{workspace}/ticket/{id}/comment/add/guest', 'TicketController@addCommentCustomer');
Route::patch('/{workspace}/ticket/{id}/comment/{cmt}/edit', 'TicketController@editComment');
Route::patch('/{workspace}/ticket/{id}/comment/{cmt}/edit/customer', 'TicketController@editCommentCustomer');
Route::delete('/{workspace}/ticket/{id}/comment/{cmt}/delete', 'TicketController@deleteComment');
Route::post('/{workspace}/ticket/{hash}/file/add', 'TicketController@addFile');
Route::get('/{workspace}/ticket/search', 'TicketController@viewSearch');
Route::post('/{workspace}/ticket/search', 'TicketController@search');
Route::patch('/{workspace}/ticket/{id}/notes/save', 'TicketController@saveNotes');
Route::get('/{workspace}/ticket/{ticketHash}/file/{id}/get', 'TicketController@getAttachment');
Route::delete('/{workspace}/ticket/{ticketHash}/file/{id}/delete', 'TicketController@deleteAttachment');
Route::post('/{workspace}/tickettype/add', 'SettingsController@addTicketType');
Route::any('/{workspace}/tickettype/{id}/edit', 'SettingsController@editTicketType');
Route::any('/{workspace}/tickettype/{id}/delete', 'SettingsController@deleteTicketType');

Route::get('/{workspace}/group/list', 'GroupsController@listGroups');
Route::get('/{workspace}/group/create', 'GroupsController@viewCreateGroup');
Route::post('/{workspace}/group/create', 'GroupsController@createGroup');
Route::get('/{workspace}/group/{id}/show', 'GroupsController@viewGroup');
Route::patch('/{workspace}/group/{id}/edit', 'GroupsController@editGroup');
Route::any('/{workspace}/group/{id}/delete', 'GroupsController@deleteGroup');

Route::get('/{workspace}/faq/list', 'FaqController@viewList');
Route::get('/{workspace}/faq/create', 'FaqController@viewCreate');
Route::post('/{workspace}/faq/create', 'FaqController@create');
Route::get('/{workspace}/faq/{id}/edit', 'FaqController@viewEdit');
Route::patch('/{workspace}/faq/{id}/edit', 'FaqController@edit');
Route::any('/{workspace}/faq/{id}/delete', 'FaqController@delete');

Route::get('/{workspace}/settings', 'SettingsController@show');
Route::get('/{workspace}/settings/agent', 'SettingsController@showAgent');
Route::patch('/{workspace}/settings/save', 'SettingsController@save');
Route::patch('/{workspace}/settings/locale', 'SettingsController@saveLocale');
Route::patch('/{workspace}/settings/tickets', 'SettingsController@saveTicketSettings');
Route::patch('/{workspace}/settings/avatar', 'SettingsController@saveAvatar');
Route::get('/{workspace}/settings/system', 'SettingsController@viewSystemSettings');
Route::patch('/{workspace}/settings/system', 'SettingsController@saveSystemSettings');
Route::post('/{workspace}/settings/system/mailer', 'SettingsController@mailer');
Route::patch('/{workspace}/settings/system/apitoken', 'SettingsController@generateApiToken');
Route::patch('/{workspace}/settings/system/widget/token', 'SettingsController@generateWidgetToken');
Route::post('/{workspace}/settings/system/widget', 'SettingsController@saveWidgetSettings');
Route::post('/{workspace}/settings/system/backgrounds/add', 'SettingsController@addBackgroundImage');
Route::any('/{workspace}/settings/system/backgrounds/delete/{name}', 'SettingsController@deleteBackgroundImage');
Route::post('/{workspace}/system/tickets/export', 'SettingsController@exportTickets');
Route::post('/{workspace}/settings/system/cancel', 'SettingsController@cancelWorkspace');

Route::any('/mailservice/self/{password}', 'MainController@mailservice');
Route::any('/mailservice/custom/{password}', 'MainController@mailservice_custom');

Route::get('/install', 'InstallerController@viewInstall');
Route::post('/install', 'InstallerController@install');

Route::post('/api/{workspace}/ticket/create', 'ApiController@createTicket');
Route::post('/api/{workspace}/ticket/info', 'ApiController@getTicketInfo');
Route::post('/api/{workspace}/ticket/thread', 'ApiController@getTicketThread');
Route::post('/api/{workspace}/ticket/attachments', 'ApiController@getTicketAttachments');
Route::post('/api/{workspace}/ticket/comment/add/customer', 'ApiController@addCustomerComment');
Route::post('/api/{workspace}/ticket/comment/edit/customer', 'ApiController@editCommentCustomer');
Route::post('/api/{workspace}/ticket/attachment/add', 'ApiController@addFile');
Route::post('/api/{workspace}/ticket/attachment/delete', 'ApiController@deleteFile');
Route::post('/api/{workspace}/widget/ticket/create', 'ApiController@widgetCreateTicket');

Route::any('/{workspace}/payment/charge', 'PaymentController@charge');

Route::get('/{workspace}', 'MainController@workspaceIndex');
