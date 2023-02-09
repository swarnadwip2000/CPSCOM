<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="submenu">
                    <a href="#" class="{{ Request::is('admin/profile*') || Request::is('admin/password*') || Request::is('admin/admin*') ? 'active' : ' ' }}"><i class="la la-user"></i> <span>Manage Account </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('admin/profile*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.profile') }}">My Profile</a>
                        </li>
                        <li class="{{ Request::is('admin/password*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.password') }}">Change Password</a>
                        </li>
                        <li class="{{ Request::is('admin/admin*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.index') }}">Admin List</a>
                        </li>                     
                    </ul>
                </li>
                <li class="{{ Request::is('admin/user*') ? 'active' : ' ' }}">
                    <a href="{{ route('user.index') }}" ><i class="la la-users"></i> <span>User List</span></a>                 
                </li>
                

                <li class="{{ Request::is('admin/sub-admin*') ? 'active' : ' ' }}">
                    <a href="{{ route('sub-admin.index') }}"><i class="fa fa-user"></i> <span>Sub Admin List</span></a>
                </li>
                <li class="{{ Request::is('admin/group*') ? 'active' : ' ' }}">
                    <a href="{{ route('group.index') }}"><i class="fa fa-list"></i> <span>Groups</span></a>
                </li>
                
            </ul> 
        </div>
    </div>
</div>


