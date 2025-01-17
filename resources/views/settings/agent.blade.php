{{--
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.settings') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <div class="tabs">
                            <ul>
                                <li id="tabGeneral" class="is-active"><a href="javascript:void(0);" onclick="window.showTabMenu('tabGeneral');">{{ __('app.general') }}</a></li>
                                <li id="tabAvatar"><a href="javascript:void(0);" onclick="window.showTabMenu('tabAvatar');">{{ __('app.settings_avatar') }}</a></li>
                                <li id="tabTickets"><a href="javascript:void(0);" onclick="window.showTabMenu('tabTickets');">{{ __('app.tickets') }}</a></li>
                                <li id="tabLanguage"><a href="javascript:void(0);" onclick="window.showTabMenu('tabLanguage');">{{ __('app.settings_language') }}</a></li>
                            </ul>
                        </div>

                        <div id="tabGeneral-form">
                            <form method="POST" action="{{ url('/' . $workspace . '/settings/save') }}">
                                @csrf
                                @method('PATCH')

                                <div class="field">
                                    <label class="label">{{ __('app.settings_surname') }}</label>
                                    <div class="control">
                                        <input type="text" name="surname" value="{{ $agent->surname }}"/>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.settings_lastname') }}</label>
                                    <div class="control">
                                        <input type="text" name="lastname" value="{{ $agent->lastname }}"/>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.settings_email') }}</label>
                                    <div class="control">
                                        <input type="text" name="email" value="{{ $agent->email }}"/>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.settings_password') }}</label>
                                    <div class="control">
                                        <input type="password" name="password"/>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.settings_password_confirmation') }}</label>
                                    <div class="control">
                                        <input type="password" name="password_confirm"/>
                                    </div>
                                </div>

                                <br/>

                                <div class="field">
                                    <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                                </div>
                            </form>

                            <br/>
                        </div>

                        <div id="tabAvatar-form" class="is-hidden">
                            <form method="POST" action="{{ url('/' . $workspace . '/settings/avatar') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="field">
                                    <label class="label">{{ __('app.settings_avatar') }}</label>
                                    <div class="control">
                                        <input type="file" name="avatar" data-role="file" data-button-title="{{ __('app.choose_file') }}"/>
                                    </div>
                                </div>

                                <br/>

                                <div class="field">
                                    <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                                </div>
                            </form>

                            <br/>
                        </div>

                        <div id="tabTickets-form" class="is-hidden">
                            <form method="POST" action="{{ url('/' . $workspace . '/settings/tickets') }}">
                                @csrf
                                @method('PATCH')

                                <div class="field">
                                    <div class="control">
                                        <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.settings_mailonticketingroup') }}" name="mailonticketingroup" value="1" <?php if ((bool)$agent->mailonticketingroup === true) echo 'checked'; ?>/>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.settings_hideclosedtickets') }}" name="hideclosedtickets" value="1" <?php if ((bool)$agent->hideclosedtickets === true) echo 'checked'; ?>/>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.settings_signature') }}</label>
                                    <div class="control">
                                        <textarea name="signature" class="textarea">{{ $agent->signature }}</textarea>
                                    </div>
                                </div>

                                <br/>

                                <div class="field">
                                    <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                                </div>
                            </form>

                            <br/>
                        </div>

                        <div id="tabLanguage-form" class="is-hidden">
                            <form method="POST" action="{{ url('/' . $workspace . '/settings/locale') }}">
                                @csrf
                                @method('PATCH')

                                <div class="field">
                                    <label class="label">{{ __('app.settings_language') }}</label>
                                    <div class="control">
                                        <select name="lang">
                                            @foreach ($langs as $lng)
                                                <option value="{{ $lng }}" <?php if ($lng === $lang) echo 'selected'; ?>>{{ locale_get_display_language($lng, $lang) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <br/>

                                <div class="field">
                                    <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                                </div>
                            </form>

                            <br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    window.showTabMenu = function(target) {
        let tabItems = ['tabGeneral', 'tabAvatar', 'tabTickets', 'tabLanguage'];

        tabItems.forEach(function(elem, index) {
            if (elem !== target) {
                document.getElementById(elem).classList.remove('is-active');
                document.getElementById(elem + '-form').classList.add('is-hidden');
            }

            document.getElementById(target).classList.add('is-active');
            document.getElementById(target + '-form').classList.remove('is-hidden');
        });
    };
@endsection
