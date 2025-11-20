<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Financial Assistance
    Route::delete('financial-assistances/destroy', 'FinancialAssistanceController@massDestroy')->name('financial-assistances.massDestroy');
    Route::post('financial-assistances/media', 'FinancialAssistanceController@storeMedia')->name('financial-assistances.storeMedia');
    Route::post('financial-assistances/ckmedia', 'FinancialAssistanceController@storeCKEditorImages')->name('financial-assistances.storeCKEditorImages');
    Route::post('financial-assistances/parse-csv-import', 'FinancialAssistanceController@parseCsvImport')->name('financial-assistances.parseCsvImport');
    Route::post('financial-assistances/process-csv-import', 'FinancialAssistanceController@processCsvImport')->name('financial-assistances.processCsvImport');
    Route::resource('financial-assistances', 'FinancialAssistanceController');
    // Put this near your other financial-assistances routes (inside the admin group)
    Route::get('financial-assistances/{financialAssistance}/print', 'FinancialAssistanceController@printCaseSummary')
        ->name('financial-assistances.print');



    // Guarantee Letter
    Route::delete('guarantee-letters/destroy', 'GuaranteeLetterController@massDestroy')->name('guarantee-letters.massDestroy');
    Route::post('guarantee-letters/parse-csv-import', 'GuaranteeLetterController@parseCsvImport')->name('guarantee-letters.parseCsvImport');
    Route::post('guarantee-letters/process-csv-import', 'GuaranteeLetterController@processCsvImport')->name('guarantee-letters.processCsvImport');
    Route::resource('guarantee-letters', 'GuaranteeLetterController');

    // Burial Assistance
    Route::delete('burial-assistances/destroy', 'BurialAssistanceController@massDestroy')->name('burial-assistances.massDestroy');
    Route::post('burial-assistances/parse-csv-import', 'BurialAssistanceController@parseCsvImport')->name('burial-assistances.parseCsvImport');
    Route::post('burial-assistances/process-csv-import', 'BurialAssistanceController@processCsvImport')->name('burial-assistances.processCsvImport');
    Route::resource('burial-assistances', 'BurialAssistanceController');

    // Medical Assistance
    Route::delete('medical-assistances/destroy', 'MedicalAssistanceController@massDestroy')->name('medical-assistances.massDestroy');
    Route::post('medical-assistances/parse-csv-import', 'MedicalAssistanceController@parseCsvImport')->name('medical-assistances.parseCsvImport');
    Route::post('medical-assistances/process-csv-import', 'MedicalAssistanceController@processCsvImport')->name('medical-assistances.processCsvImport');
    Route::resource('medical-assistances', 'MedicalAssistanceController');

    // Solicitation
    Route::delete('solicitations/destroy', 'SolicitationController@massDestroy')->name('solicitations.massDestroy');
    Route::post('solicitations/parse-csv-import', 'SolicitationController@parseCsvImport')->name('solicitations.parseCsvImport');
    Route::post('solicitations/process-csv-import', 'SolicitationController@processCsvImport')->name('solicitations.processCsvImport');
    Route::resource('solicitations', 'SolicitationController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Contact Company
    Route::delete('contact-companies/destroy', 'ContactCompanyController@massDestroy')->name('contact-companies.massDestroy');
    Route::post('contact-companies/parse-csv-import', 'ContactCompanyController@parseCsvImport')->name('contact-companies.parseCsvImport');
    Route::post('contact-companies/process-csv-import', 'ContactCompanyController@processCsvImport')->name('contact-companies.processCsvImport');
    Route::resource('contact-companies', 'ContactCompanyController');

    // Contact Contacts
    Route::delete('contact-contacts/destroy', 'ContactContactsController@massDestroy')->name('contact-contacts.massDestroy');
    Route::resource('contact-contacts', 'ContactContactsController');

    // Task Status
    Route::delete('task-statuses/destroy', 'TaskStatusController@massDestroy')->name('task-statuses.massDestroy');
    Route::resource('task-statuses', 'TaskStatusController');

    // Task Tag
    Route::delete('task-tags/destroy', 'TaskTagController@massDestroy')->name('task-tags.massDestroy');
    Route::resource('task-tags', 'TaskTagController');

    // Task
    Route::delete('tasks/destroy', 'TaskController@massDestroy')->name('tasks.massDestroy');
    Route::post('tasks/media', 'TaskController@storeMedia')->name('tasks.storeMedia');
    Route::post('tasks/ckmedia', 'TaskController@storeCKEditorImages')->name('tasks.storeCKEditorImages');
    Route::post('tasks/parse-csv-import', 'TaskController@parseCsvImport')->name('tasks.parseCsvImport');
    Route::post('tasks/process-csv-import', 'TaskController@processCsvImport')->name('tasks.processCsvImport');
    Route::resource('tasks', 'TaskController');

    // Tasks Calendar
    Route::resource('tasks-calendars', 'TasksCalendarController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Ngo
    Route::delete('ngos/destroy', 'NgoController@massDestroy')->name('ngos.massDestroy');
    Route::resource('ngos', 'NgoController');

    // Sector Group
    Route::delete('sector-groups/destroy', 'SectorGroupController@massDestroy')->name('sector-groups.massDestroy');
    Route::resource('sector-groups', 'SectorGroupController');

    // Barangay
    Route::delete('barangays/destroy', 'BarangayController@massDestroy')->name('barangays.massDestroy');
    Route::post('barangays/parse-csv-import', 'BarangayController@parseCsvImport')->name('barangays.parseCsvImport');
    Route::post('barangays/process-csv-import', 'BarangayController@processCsvImport')->name('barangays.processCsvImport');
    Route::resource('barangays', 'BarangayController');

    // Directory
    Route::delete('directories/destroy', 'DirectoryController@massDestroy')->name('directories.massDestroy');
    Route::post('directories/media', 'DirectoryController@storeMedia')->name('directories.storeMedia');
    Route::post('directories/ckmedia', 'DirectoryController@storeCKEditorImages')->name('directories.storeCKEditorImages');
    Route::post('directories/parse-csv-import', 'DirectoryController@parseCsvImport')->name('directories.parseCsvImport');
    Route::post('directories/process-csv-import', 'DirectoryController@processCsvImport')->name('directories.processCsvImport');
    Route::resource('directories', 'DirectoryController');




    Route::post('directories/match/upload', 'DirectoryController@matchUpload')->name('directories.match.upload');
    Route::get('directories/match/download', 'DirectoryController@matchDownload')->name('directories.match.download');



    // Familycomposition
    Route::delete('familycompositions/destroy', 'FamilycompositionController@massDestroy')->name('familycompositions.massDestroy');
    Route::resource('familycompositions', 'FamilycompositionController');

    Route::get('messenger', 'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
