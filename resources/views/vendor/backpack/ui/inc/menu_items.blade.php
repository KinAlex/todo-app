{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if (backpack_auth()->user()->is_admin)
    <x-backpack::menu-item title="Users" icon="la la-question" :link="backpack_url('user')" />
@endif
<x-backpack::menu-item title="Todo tasks" icon="la la-question" :link="backpack_url('todo-tasks')" />
