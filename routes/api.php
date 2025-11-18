<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Financial Assistance
    Route::post('financial-assistances/media', 'FinancialAssistanceApiController@storeMedia')->name('financial-assistances.storeMedia');
    Route::apiResource('financial-assistances', 'FinancialAssistanceApiController');

    // Guarantee Letter
    Route::apiResource('guarantee-letters', 'GuaranteeLetterApiController');

    // Burial Assistance
    Route::apiResource('burial-assistances', 'BurialAssistanceApiController');

    // Medical Assistance
    Route::apiResource('medical-assistances', 'MedicalAssistanceApiController');

    // Solicitation
    Route::apiResource('solicitations', 'SolicitationApiController');

    // Contact Company
    Route::apiResource('contact-companies', 'ContactCompanyApiController');

    // Task
    Route::post('tasks/media', 'TaskApiController@storeMedia')->name('tasks.storeMedia');
    Route::apiResource('tasks', 'TaskApiController');

    // Ngo
    Route::apiResource('ngos', 'NgoApiController');

    // Sector Group
    Route::apiResource('sector-groups', 'SectorGroupApiController');

    // Barangay
    Route::apiResource('barangays', 'BarangayApiController');

    // Directory
    Route::post('directories/media', 'DirectoryApiController@storeMedia')->name('directories.storeMedia');
    Route::apiResource('directories', 'DirectoryApiController');

    // Familycomposition
    Route::apiResource('familycompositions', 'FamilycompositionApiController');
});
