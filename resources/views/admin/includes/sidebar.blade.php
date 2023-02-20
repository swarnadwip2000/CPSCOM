<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <span>Main</span>
                </li>

                <li class="submenu">
                    <a href="#" class="{{ Request::is('admin/profile*') || Request::is('admin/password*') || Request::is('admin/admin*') ? 'active' : ' ' }}"><i class="la la-user-cog"></i> <span>Manage Account </span> <span
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
                <li class="{{ Request::is('admin/teams*') ? 'active' : ' ' }}">
                    <a href="{{ route('sub-admin.index') }}" ><i class="la la-users"></i> <span>Teams</span></a>                 
                </li>
                

                <li class="{{ Request::is('admin/team-x-members*') ? 'active' : ' ' }}">
                    <a href="{{ route('user.index') }}"><i class="la la-user"></i> <span>Team x Members</span></a>
                </li>
                <li class="{{ Request::is('admin/team-x-group*') ? 'active' : ' ' }}">
                    <a href="{{ route('group.index') }}"><i class="la la-list"></i> <span>Team x Groups</span></a>
                </li>

                <li class="menu-title">
                    <span>Content Management System</span>
                </li>
                <li class="submenu">
                    <a href="#" class="{{ Request::is('cms/sub-admin*') ? 'active' : ' ' }}"><i class="la la-address-card"></i> <span>Team Panel </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('cms/sub-admin*') ? 'active' : ' ' }}">
                            <a href="{{ route('cms.sub-admin.get-started') }}">Get Started Page</a>
                        </li>                   
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#" class="{{ Request::is('cms/user*') ? 'active' : ' '}}"><i class="la la-newspaper"></i> <span>Team x Member Panel </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('cms/user*') ? 'active' : ' ' }}">
                            <a href="{{ route('cms.user.get-started') }}">Get Started Page</a>
                        </li>                  
                    </ul>
                </li>
               
            </ul> 
        </div>
    </div>
</div>


