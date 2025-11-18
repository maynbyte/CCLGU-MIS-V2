<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs("admin.home") ? "active" : "" }}" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('directory_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.directories.index") }}" class="nav-link {{ request()->is("admin/directories") || request()->is("admin/directories/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon far fa-address-card">

                            </i>
                            <p>
                                {{ trans('cruds.directory.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('service_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/financial-assistances*") ? "menu-open" : "" }} {{ request()->is("admin/guarantee-letters*") ? "menu-open" : "" }} {{ request()->is("admin/burial-assistances*") ? "menu-open" : "" }} {{ request()->is("admin/medical-assistances*") ? "menu-open" : "" }} {{ request()->is("admin/solicitations*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/financial-assistances*") ? "active" : "" }} {{ request()->is("admin/guarantee-letters*") ? "active" : "" }} {{ request()->is("admin/burial-assistances*") ? "active" : "" }} {{ request()->is("admin/medical-assistances*") ? "active" : "" }} {{ request()->is("admin/solicitations*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-list-ul">

                            </i>
                            <p>
                                {{ trans('cruds.service.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('financial_assistance_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.financial-assistances.index") }}" class="nav-link {{ request()->is("admin/financial-assistances") || request()->is("admin/financial-assistances/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-hand-holding-heart">

                                        </i>
                                        <p>
                                            {{ trans('cruds.financialAssistance.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('guarantee_letter_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.guarantee-letters.index") }}" class="nav-link {{ request()->is("admin/guarantee-letters") || request()->is("admin/guarantee-letters/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-gofore">

                                        </i>
                                        <p>
                                            {{ trans('cruds.guaranteeLetter.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('burial_assistance_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.burial-assistances.index") }}" class="nav-link {{ request()->is("admin/burial-assistances") || request()->is("admin/burial-assistances/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-pagelines">

                                        </i>
                                        <p>
                                            {{ trans('cruds.burialAssistance.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('medical_assistance_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.medical-assistances.index") }}" class="nav-link {{ request()->is("admin/medical-assistances") || request()->is("admin/medical-assistances/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-notes-medical">

                                        </i>
                                        <p>
                                            {{ trans('cruds.medicalAssistance.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('solicitation_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.solicitations.index") }}" class="nav-link {{ request()->is("admin/solicitations") || request()->is("admin/solicitations/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-newspaper">

                                        </i>
                                        <p>
                                            {{ trans('cruds.solicitation.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('contact_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/contact-companies*") ? "menu-open" : "" }} {{ request()->is("admin/contact-contacts*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/contact-companies*") ? "active" : "" }} {{ request()->is("admin/contact-contacts*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-phone-square">

                            </i>
                            <p>
                                {{ trans('cruds.contactManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('contact_company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.contact-companies.index") }}" class="nav-link {{ request()->is("admin/contact-companies") || request()->is("admin/contact-companies/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-building">

                                        </i>
                                        <p>
                                            {{ trans('cruds.contactCompany.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('contact_contact_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.contact-contacts.index") }}" class="nav-link {{ request()->is("admin/contact-contacts") || request()->is("admin/contact-contacts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-plus">

                                        </i>
                                        <p>
                                            {{ trans('cruds.contactContact.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('task_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/task-statuses*") ? "menu-open" : "" }} {{ request()->is("admin/task-tags*") ? "menu-open" : "" }} {{ request()->is("admin/tasks*") ? "menu-open" : "" }} {{ request()->is("admin/tasks-calendars*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/task-statuses*") ? "active" : "" }} {{ request()->is("admin/task-tags*") ? "active" : "" }} {{ request()->is("admin/tasks*") ? "active" : "" }} {{ request()->is("admin/tasks-calendars*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-list">

                            </i>
                            <p>
                                {{ trans('cruds.taskManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('task_status_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.task-statuses.index") }}" class="nav-link {{ request()->is("admin/task-statuses") || request()->is("admin/task-statuses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-server">

                                        </i>
                                        <p>
                                            {{ trans('cruds.taskStatus.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('task_tag_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.task-tags.index") }}" class="nav-link {{ request()->is("admin/task-tags") || request()->is("admin/task-tags/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-server">

                                        </i>
                                        <p>
                                            {{ trans('cruds.taskTag.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('task_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.tasks.index") }}" class="nav-link {{ request()->is("admin/tasks") || request()->is("admin/tasks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.task.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('tasks_calendar_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.tasks-calendars.index") }}" class="nav-link {{ request()->is("admin/tasks-calendars") || request()->is("admin/tasks-calendars/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-calendar">

                                        </i>
                                        <p>
                                            {{ trans('cruds.tasksCalendar.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/audit-logs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/permissions*") ? "active" : "" }} {{ request()->is("admin/roles*") ? "active" : "" }} {{ request()->is("admin/users*") ? "active" : "" }} {{ request()->is("admin/audit-logs*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('audit_log_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.audit-logs.index") }}" class="nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.auditLog.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('setting_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/user-alerts*") ? "menu-open" : "" }} {{ request()->is("admin/ngos*") ? "menu-open" : "" }} {{ request()->is("admin/sector-groups*") ? "menu-open" : "" }} {{ request()->is("admin/barangays*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/user-alerts*") ? "active" : "" }} {{ request()->is("admin/ngos*") ? "active" : "" }} {{ request()->is("admin/sector-groups*") ? "active" : "" }} {{ request()->is("admin/barangays*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.setting.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('user_alert_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.user-alerts.index") }}" class="nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-bell">

                                        </i>
                                        <p>
                                            {{ trans('cruds.userAlert.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('ngo_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.ngos.index") }}" class="nav-link {{ request()->is("admin/ngos") || request()->is("admin/ngos/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.ngo.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('sector_group_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.sector-groups.index") }}" class="nav-link {{ request()->is("admin/sector-groups") || request()->is("admin/sector-groups/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.sectorGroup.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('barangay_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.barangays.index") }}" class="nav-link {{ request()->is("admin/barangays") || request()->is("admin/barangays/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.barangay.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('familycomposition_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.familycompositions.index") }}" class="nav-link {{ request()->is("admin/familycompositions") || request()->is("admin/familycompositions/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.familycomposition.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @php($unread = \App\Models\QaTopic::unreadCount())
                    <li class="nav-item">
                        <a href="{{ route("admin.messenger.index") }}" class="{{ request()->is("admin/messenger") || request()->is("admin/messenger/*") ? "active" : "" }} nav-link">
                            <i class="fa-fw fa fa-envelope nav-icon">

                            </i>
                            <p>{{ trans('global.messages') }}</p>
                            @if($unread > 0)
                                <strong>( {{ $unread }} )</strong>
                            @endif

                        </a>
                    </li>
                    @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                        @can('profile_password_edit')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                                    <i class="fa-fw fas fa-key nav-icon">
                                    </i>
                                    <p>
                                        {{ trans('global.change_password') }}
                                    </p>
                                </a>
                            </li>
                        @endcan
                    @endif
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                            <p>
                                <i class="fas fa-fw fa-sign-out-alt nav-icon">

                                </i>
                                <p>{{ trans('global.logout') }}</p>
                            </p>
                        </a>
                    </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>