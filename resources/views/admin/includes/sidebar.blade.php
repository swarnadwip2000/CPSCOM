<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="{{ Request::is('admin/user*') ? 'active' : ' ' }}">
                    <a href="{{ route('user.index') }}"><i class="la la-users"></i> <span>Users</span></a>
                </li>
                <li class="{{ Request::is('admin/sub-admin*') ? 'active' : ' ' }}">
                    <a href="{{ route('sub-admin.index') }}"><i class="fa fa-user"></i> <span>Sub Admin</span></a>
                </li>
                <li class="{{ Request::is('admin/group*') ? 'active' : ' ' }}">
                    <a href="{{ route('group.index') }}"><i class="fa fa-list"></i> <span>Groups</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>
